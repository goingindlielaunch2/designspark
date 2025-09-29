<?php
// Test email functionality
// Run this script to test if email is working on your server

$test_email = "hello@withdesignspark.com";
$test_subject = "Email Test - " . date('Y-m-d H:i:s');
$test_message = "This is a test email to verify email functionality is working.\n\nSent from: " . $_SERVER['HTTP_HOST'] . "\nTime: " . date('Y-m-d H:i:s');

echo "<h2>Email Function Test</h2>";

// Test 1: Basic mail() function
echo "<h3>Test 1: Basic mail() function</h3>";
$headers = "From: test@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$result1 = mail($test_email, $test_subject, $test_message, $headers);
echo "Result: " . ($result1 ? "SUCCESS" : "FAILED") . "<br>";

if (!$result1) {
    $last_error = error_get_last();
    echo "Last error: " . ($last_error ? $last_error['message'] : "No specific error") . "<br>";
}

// Test 2: Check mail configuration
echo "<h3>Test 2: PHP Mail Configuration</h3>";
echo "sendmail_path: " . ini_get('sendmail_path') . "<br>";
echo "SMTP: " . ini_get('SMTP') . "<br>";
echo "smtp_port: " . ini_get('smtp_port') . "<br>";
echo "sendmail_from: " . ini_get('sendmail_from') . "<br>";

// Test 3: Check if sendmail exists
echo "<h3>Test 3: Sendmail Check</h3>";
$sendmail_paths = ['/usr/sbin/sendmail', '/usr/bin/sendmail', '/bin/sendmail'];
foreach ($sendmail_paths as $path) {
    echo $path . ": " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "<br>";
}

// Test 4: Server info
echo "<h3>Test 4: Server Information</h3>";
echo "Server: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OS: " . php_uname() . "<br>";

// Test 5: Create log file test
echo "<h3>Test 5: File Logging Test</h3>";
$log_file = __DIR__ . '/email_test.log';
$log_content = "Email test run at: " . date('Y-m-d H:i:s') . "\n";
$log_result = file_put_contents($log_file, $log_content, FILE_APPEND);
echo "Log file creation: " . ($log_result ? "SUCCESS" : "FAILED") . "<br>";
if ($log_result) {
    echo "Log file location: " . $log_file . "<br>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ul>";
echo "<li>If Test 1 shows SUCCESS, check your email for the test message</li>";
echo "<li>If Test 1 shows FAILED, your server's mail function is not working</li>";
echo "<li>Check the PHP mail configuration in Test 2</li>";
echo "<li>If no email arrives but Test 1 shows SUCCESS, check your spam folder</li>";
echo "</ul>";

echo "<p><strong>Alternative Solutions:</strong></p>";
echo "<ul>";
echo "<li>Set up SMTP with a service like Gmail, SendGrid, or Mailgun</li>";
echo "<li>Use a webhook service like Zapier to forward form submissions</li>";
echo "<li>Set up email forwarding in your hosting control panel</li>";
echo "</ul>";
?>
