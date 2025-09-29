<?php
// File: delete.php
// =================================================================
// Handles the deletion of entries from the database.
// =================================================================

require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if ID and type are set
if (isset($_POST['id']) && isset($_POST['type'])) {
    $conn = get_db_connection();
    
    $id = (int)$_POST['id'];
    $type = $_POST['type'];
    
    $table_name = '';

    // Determine the table name based on the type
    if ($type === 'evaluation') {
        $table_name = 'evaluations';
    } elseif ($type === 'contact') {
        $table_name = 'contacts';
    }

    // If a valid table name is found, proceed with deletion
    if ($id > 0 && !empty($table_name)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM $table_name WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
}

// Redirect back to the admin dashboard
header('Location: admin.php');
exit;
?>
