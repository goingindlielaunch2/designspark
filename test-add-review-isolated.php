<?php
// Minimal test of add review functionality
require_once 'manageone/config.php';
require_once 'review-functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Analysis:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test the exact same logic as admin.php
    $action = $_POST['action'] ?? '';
    echo "<p>Action: '$action'</p>";
    echo "<p>Action length: " . strlen($action) . "</p>";
    echo "<p>Action hex: " . bin2hex($action) . "</p>";
    
    if ($action === 'add_review') {
        echo "<p style='color: green;'>✓ ADD REVIEW handler would execute</p>";
        
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerEmail = trim($_POST['customer_email'] ?? '');
        $websiteUrl = trim($_POST['website_url'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $reviewText = trim($_POST['review_text'] ?? '');
        $approved = isset($_POST['approved']) && $_POST['approved'] === '1';
        $featured = isset($_POST['featured']) && $_POST['featured'] === '1';
        
        echo "<p>Approved: " . ($approved ? 'TRUE' : 'FALSE') . "</p>";
        echo "<p>Featured: " . ($featured ? 'TRUE' : 'FALSE') . "</p>";
        
        if (!empty($customerName) && !empty($reviewText)) {
            $reviewId = storeReview($customerName, $customerEmail, $websiteUrl, $rating, $reviewText);
            if ($reviewId) {
                echo "<p style='color: green;'>✓ Review stored with ID: $reviewId</p>";
                
                if ($approved || $featured) {
                    $statusResult = updateReviewStatus($reviewId, $approved, $featured);
                    echo "<p>Status update: " . ($statusResult ? 'SUCCESS' : 'FAILED') . "</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Failed to store review</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Missing required fields</p>";
        }
    } elseif ($action === 'update_review') {
        echo "<p style='color: orange;'>⚠ UPDATE REVIEW handler would execute (unexpected!)</p>";
    } else {
        echo "<p style='color: red;'>✗ No matching handler for action: '$action'</p>";
    }
    
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Isolated Add Review Test</title>
</head>
<body>
    <h2>Isolated Add Review Test</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add_review">
        
        <label>Customer Name *:</label><br>
        <input type="text" name="customer_name" value="Test Customer" required><br><br>
        
        <label>Customer Email:</label><br>
        <input type="email" name="customer_email" value="test@example.com"><br><br>
        
        <label>Website URL:</label><br>
        <input type="url" name="website_url" value="https://example.com"><br><br>
        
        <label>Rating *:</label><br>
        <select name="rating">
            <option value="5">5 stars</option>
            <option value="4">4 stars</option>
        </select><br><br>
        
        <label>Review Text *:</label><br>
        <textarea name="review_text" required>This is an isolated test review</textarea><br><br>
        
        <label>
            <input type="checkbox" name="approved" value="1" checked> Approve immediately
        </label><br>
        
        <label>
            <input type="checkbox" name="featured" value="1"> Mark as featured
        </label><br><br>
        
        <button type="submit">Test Add Review</button>
    </form>
</body>
</html>
