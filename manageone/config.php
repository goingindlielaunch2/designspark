<?php
// File: config.php
// =================================================================
// This file contains the database and admin login configurations.
// =================================================================

// Gemini API Key
define('GEMINI_API_KEY', 'AIzaSyC_JE5rLIUpz2lZLzmMFUh4cbRJJyrR9pM');

// --- DATABASE CONFIGURATION ---
// Replace with your actual database credentials.
define('DB_SERVER', 'localhost'); // e.g., 'localhost' or your server IP
define('DB_USERNAME', 'rfvbydmy_01xconnect0ne');
define('DB_PASSWORD', 'jupbiH-witxur-tevki6!');
define('DB_NAME', 'rfvbydmy_designspark');

// --- ADMIN LOGIN CREDENTIALS ---
// IMPORTANT: Change these to something secure.
define('ADMIN_USER', 'syncdin1@icloud.com');
define('ADMIN_PASS', 'itxur-tevki6!');

// --- EMAIL CONFIGURATION (NEW) ---
// The email address that messages will be sent from.
// Replace 'no-reply@yourwebsite.com' with an email address you control.
define('FROM_EMAIL', 'no-reply@withdesignspark.com');


// --- HELPER FUNCTION TO CONNECT TO DATABASE ---
function get_db_connection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Start the session for login management across all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
