<?php
// File: send_email.php (Fixed Version 2)
// =================================================================
// Handles sending single and bulk emails with improved error handling and configuration checks.
// =================================================================

// --- For Debugging Only ---
// These lines will show any PHP errors on the screen.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use a more robust path to require the configuration file.
// This ensures it's found regardless of how the script is run.
require_once __DIR__ . '/config.php';

// --- Configuration Check ---
// After including config.php, check if the required constant is actually defined.
// This prevents the fatal error and gives a clear message if config is missing/wrong.
if (!defined('FROM_EMAIL')) {
    die('ERROR: The FROM_EMAIL constant is not defined. Please ensure your <strong>config.php</strong> file is correct and contains the line: <pre>define(\'FROM_EMAIL\', \'your-email@example.com\');</pre>');
}

// Check if the user is logged in and authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Stop execution if not logged in
    die('Authentication required. Please log in.');
}

// Ensure the script was accessed via a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// --- Gather and validate common variables ---
$type = $_POST['type'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';
$headers = 'From: ' . FROM_EMAIL; // This line will now work correctly.

if (empty($subject) || empty($message) || empty($type)) {
    $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Missing required fields. Please provide a subject and message.'];
    header('Location: admin.php?page=messages');
    exit;
}

// --- Process email based on type ---
$email_sent_count = 0;
$email_failed_count = 0;

if ($type === 'single') {
    $to_email = $_POST['to_email'] ?? '';
    if (filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        // Added @ to suppress default mail warnings, as we have custom feedback.
        if (@mail($to_email, $subject, $message, $headers)) {
            $email_sent_count++;
        } else {
            $email_failed_count++;
        }
    } else {
        $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Invalid "To" email address provided.'];
        header('Location: admin.php?page=messages');
        exit;
    }
} elseif ($type === 'bulk') {
    // Connect to the database only when needed for bulk mail
    $conn = get_db_connection();
    $result = $conn->query("SELECT email FROM evaluations WHERE subscribed_newsletter = 1");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Added @ to suppress default mail warnings.
            if (@mail($row['email'], $subject, $message, $headers)) {
                $email_sent_count++;
            } else {
                $email_failed_count++;
            }
        }
    }
    $conn->close();
} else {
    $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Invalid message type specified.'];
    header('Location: admin.php?page=messages');
    exit;
}

// --- Set a clear feedback message based on the outcome ---
if ($email_sent_count > 0 && $email_failed_count == 0) {
    $_SESSION['feedback_message'] = ['type' => 'success', 'text' => "Successfully sent {$email_sent_count} email(s)."];
} elseif ($email_sent_count > 0 && $email_failed_count > 0) {
    $_SESSION['feedback_message'] = ['type' => 'error', 'text' => "Sent {$email_sent_count} email(s), but failed to send {$email_failed_count}. Please check server mail logs."];
} elseif ($email_sent_count == 0 && $email_failed_count > 0) {
    $_SESSION['feedback_message'] = ['type' => 'error', 'text' => "Failed to send email(s). Your server's mail function may be misconfigured."];
} elseif ($email_sent_count == 0 && $email_failed_count == 0) {
    // This handles the case where a bulk message is sent to zero subscribers
    $_SESSION['feedback_message'] = ['type' => 'success', 'text' => 'No emails were sent. There may be no subscribers for a bulk message.'];
}

// --- Redirect back to the messages page ---
header('Location: admin.php?page=messages');
exit;

?>
