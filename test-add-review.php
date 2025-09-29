<?php
// Test script to debug add review functionality
require_once 'review-functions.php';

echo "<h1>Testing Add Review Functionality</h1>";

// Test database connection
echo "<h2>1. Testing Database Connection</h2>";
$conn = get_db_connection();
if ($conn) {
    echo "✅ Database connection successful<br>";
    
    // Check if reviews table exists
    echo "<h2>2. Checking Reviews Table</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'reviews'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Reviews table exists<br>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE reviews");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Reviews table does not exist<br>";
        echo "Creating reviews table...<br>";
        
        $createTable = "
        CREATE TABLE reviews (
            id int(11) NOT NULL AUTO_INCREMENT,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) DEFAULT NULL,
            website_url varchar(255) DEFAULT NULL,
            rating int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review_text text NOT NULL,
            report_id varchar(255) DEFAULT NULL,
            session_id varchar(255) DEFAULT NULL,
            approved tinyint(1) DEFAULT 0,
            featured tinyint(1) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        if ($conn->query($createTable)) {
            echo "✅ Reviews table created successfully<br>";
        } else {
            echo "❌ Error creating reviews table: " . $conn->error . "<br>";
        }
    }
    
    $conn->close();
} else {
    echo "❌ Database connection failed<br>";
}

// Test storeReview function
echo "<h2>3. Testing storeReview Function</h2>";
$testName = "Test Customer";
$testEmail = "test@example.com";
$testUrl = "https://example.com";
$testRating = 5;
$testReview = "This is a test review from the debug script.";

echo "Attempting to store test review...<br>";
$reviewId = storeReview($testName, $testEmail, $testUrl, $testRating, $testReview);

if ($reviewId) {
    echo "✅ Review stored successfully with ID: " . $reviewId . "<br>";
} else {
    echo "❌ Failed to store review<br>";
}

echo "<h2>4. Recent Error Log Entries</h2>";
echo "<p>Check your server error logs for detailed error messages.</p>";

// Show current reviews
echo "<h2>5. Current Reviews in Database</h2>";
$conn = get_db_connection();
if ($conn) {
    $result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Customer Name</th><th>Rating</th><th>Review Text</th><th>Approved</th><th>Featured</th><th>Created At</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
            echo "<td>" . $row['rating'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['review_text'], 0, 50)) . "...</td>";
            echo "<td>" . ($row['approved'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['featured'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No reviews found in database.<br>";
    }
    $conn->close();
}

echo "<br><br>";
echo "<strong>Instructions:</strong><br>";
echo "1. Run this script by visiting: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br>";
echo "2. Check the results above<br>";
echo "3. Try adding a review through the admin interface<br>";
echo "4. Check your server error logs for any PHP errors<br>";
echo "5. Delete this file when done testing<br>";
?>
