<?php
// Simple form data debugging script
// Use this to test what exactly gets posted

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>Individual Field Analysis:</h3>";
    echo "Action: '" . ($_POST['action'] ?? 'NOT SET') . "'<br>";
    echo "Action Length: " . strlen($_POST['action'] ?? '') . "<br>";
    echo "Action Hex: " . bin2hex($_POST['action'] ?? '') . "<br>";
    echo "Approved: " . (isset($_POST['approved']) ? $_POST['approved'] : 'NOT SET') . "<br>";
    echo "Featured: " . (isset($_POST['featured']) ? $_POST['featured'] : 'NOT SET') . "<br>";
    
    echo "<h3>Checkbox Logic Test:</h3>";
    $approved = isset($_POST['approved']) && $_POST['approved'] === '1';
    $featured = isset($_POST['featured']) && $_POST['featured'] === '1';
    echo "Approved boolean: " . ($approved ? 'TRUE' : 'FALSE') . "<br>";
    echo "Featured boolean: " . ($featured ? 'TRUE' : 'FALSE') . "<br>";
    
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Debug Test</title>
</head>
<body>
    <h2>Test Add Review Form</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add_review">
        
        <label>Customer Name:</label><br>
        <input type="text" name="customer_name" value="Test Customer" required><br><br>
        
        <label>Customer Email:</label><br>
        <input type="email" name="customer_email" value="test@example.com"><br><br>
        
        <label>Website URL:</label><br>
        <input type="url" name="website_url" value="https://example.com"><br><br>
        
        <label>Rating:</label><br>
        <select name="rating">
            <option value="5">5 stars</option>
            <option value="4">4 stars</option>
        </select><br><br>
        
        <label>Review Text:</label><br>
        <textarea name="review_text" required>This is a test review</textarea><br><br>
        
        <label>
            <input type="checkbox" name="approved" value="1" checked> Approve immediately
        </label><br>
        
        <label>
            <input type="checkbox" name="featured" value="1"> Mark as featured
        </label><br><br>
        
        <button type="submit">Submit Test</button>
    </form>
</body>
</html>
