<?php
// File: unsubscribe.php
// =================================================================
// This public-facing script handles user unsubscribe requests.
// =================================================================

require_once __DIR__ . '/config.php';

// Check for required parameters in the URL
if (isset($_GET['email']) && isset($_GET['token'])) {
    
    $email = trim($_GET['email']);
    $token = trim($_GET['token']);

    // Validate the token to ensure the request is legitimate
    $expected_token = hash('sha256', $email . UNSUBSCRIBE_SECRET_KEY);

    if (hash_equals($expected_token, $token)) {
        // Token is valid, proceed with unsubscribing the user
        $conn = get_db_connection();
        
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE evaluations SET subscribed_newsletter = 0 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $success = $stmt->affected_rows > 0;
        
        $stmt->close();
        $conn->close();
        
        $message_title = "Unsubscribe Successful";
        $message_body = "You have been successfully unsubscribed from our mailing list. You will no longer receive marketing emails from us.";

    } else {
        // Token is invalid
        $message_title = "Unsubscribe Failed";
        $message_body = "The unsubscribe link is invalid or has expired. Please try again or contact support if the issue persists.";
    }
} else {
    // Parameters are missing
    $message_title = "Invalid Request";
    $message_body = "The unsubscribe link is incomplete. Please use the link provided in the email.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($message_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($message_title); ?></h1>
        <p class="text-gray-600"><?php echo htmlspecialchars($message_body); ?></p>
        <a href="/" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
            Return to Homepage
        </a>
        <div class="mt-8 pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                6211 S Highland Dr #4060<br>
                Holladay, UT 84121
            </p>
        </div>
    </div>
</body>
</html>
