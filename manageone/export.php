<?php
// File: export.php
// =================================================================
// Handles exporting data to a CSV file.
// =================================================================

require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['type'])) {
    $conn = get_db_connection();
    $type = $_GET['type'];
    $table_name = '';
    $headers = [];
    $data_query = '';

    if ($type === 'evaluations') {
        $table_name = 'evaluations';
        $headers = ['ID', 'Name', 'Email', 'Website URL', 'Subscribed to Newsletter', 'Submission Date'];
        $data_query = "SELECT id, name, email, website_url, subscribed_newsletter, submission_date FROM evaluations ORDER BY submission_date DESC";
    } elseif ($type === 'contacts') {
        $table_name = 'contacts';
        $headers = ['ID', 'Name', 'Email', 'Subject', 'Message', 'Submission Date'];
        $data_query = "SELECT id, name, email, subject, message, submission_date FROM contacts ORDER BY submission_date DESC";
    }

    if (!empty($table_name)) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $table_name . '_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        $result = $conn->query($data_query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Convert boolean for newsletter to Yes/No for clarity in export
                if ($type === 'evaluations') {
                    $row['subscribed_newsletter'] = $row['subscribed_newsletter'] ? 'Yes' : 'No';
                }
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        $conn->close();
        exit();
    }
}

// Redirect if type is not specified
header('Location: admin.php');
exit;
?>
