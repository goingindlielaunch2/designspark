<?php
// File: process-contact-form.php
// =================================================================
// This script receives data from the public contact form,
// sanitizes it, and inserts it into the 'contacts' database table.
// It now returns a JSON response for AJAX requests.
// =================================================================

// Use a more robust path to require the configuration file.
require_once __DIR__ . '/manageone/config.php';

// Set the content type to JSON for the response
header('Content-Type: application/json');

// Function to send notification email
function send_notification_email($name, $email, $message) {
    $admin_email = "hello@withdesignspark.com";
    $email_subject = "New Contact Form Submission - " . $name;
    $email_body = "A new contact form submission has been received:\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Message:\n" . $message . "\n\n";
    $email_body .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";
    $email_body .= "Server: " . $_SERVER['HTTP_HOST'] . "\n";
    
    // Create proper email headers
    $email_headers = "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_headers .= "From: Contact Form <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
    $email_headers .= "Reply-To: " . $email . "\r\n";
    $email_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Send the email
    $mail_sent = mail($admin_email, $email_subject, $email_body, $email_headers);
    
    if ($mail_sent) {
        error_log("Contact form notification sent successfully to: " . $admin_email);
        return true;
    } else {
        error_log("Failed to send contact form notification to: " . $admin_email);
        return false;
    }
}

// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Make sure the config file was loaded correctly
    if (!function_exists('get_db_connection')) {
        echo json_encode(['success' => false, 'message' => 'Error: Configuration file is not loaded correctly.']);
        exit();
    }

    $conn = get_db_connection();

    // Get data from the form and sanitize it
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    // Assign a default subject for these submissions
    $subject = "Website Contact Form Submission";

    // Basic validation
    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        
        // Prepare an SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        // Execute the statement and return a JSON response
        if ($stmt->execute()) {
            // Send notification email to admin
            $email_sent = send_notification_email($name, $email, $message);
            
            if ($email_sent) {
                echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
            } else {
                // Email failed but database insert succeeded
                echo json_encode(['success' => true, 'message' => 'Message sent successfully! (Admin notification may have failed)']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: Could not save your message.']);
        }

        $stmt->close();
    } else {
        // Return an error JSON response for invalid input
        echo json_encode(['success' => false, 'message' => 'Invalid input. Please fill out all required fields correctly.']);
    }

    $conn->close();
} else {
    // Return an error if not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
exit();
?>