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
            echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
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