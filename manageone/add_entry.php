<?php
// File: add_entry.php
// =================================================================
// Handles adding new entries to the database from the admin panel.
// =================================================================

require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db_connection();
    
    $type = $_POST['type'] ?? '';

    if ($type === 'evaluation') {
        // Handle adding a new evaluation
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $subscribed = isset($_POST['subscribed_newsletter']) ? 1 : 0;

        if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($website_url) && filter_var($website_url, FILTER_VALIDATE_URL)) {
            $stmt = $conn->prepare("INSERT INTO evaluations (name, email, website_url, subscribed_newsletter) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $email, $website_url, $subscribed);
            
            if ($stmt->execute()) {
                $_SESSION['feedback_message'] = ['type' => 'success', 'text' => 'New evaluation entry added successfully.'];
            } else {
                $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Failed to add evaluation entry.'];
            }
            $stmt->close();
        } else {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Invalid data provided for evaluation entry. Please check all fields.'];
        }

    } elseif ($type === 'contact') {
        // Handle adding a new contact
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? 'No Subject');
        $message = trim($_POST['message'] ?? '');

        if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                $_SESSION['feedback_message'] = ['type' => 'success', 'text' => 'New contact submission added successfully.'];
            } else {
                $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Failed to add contact submission.'];
            }
            $stmt->close();
        } else {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Invalid data provided for contact submission. Please check all fields.'];
        }
    }

    $conn->close();
    // Redirect to the dashboard to see the new entry
    header('Location: admin.php?page=dashboard');
    exit;
}

// Redirect if accessed directly without POST
header('Location: admin.php');
exit;
?>
