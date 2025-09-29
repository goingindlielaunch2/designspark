<?php
// Debug script to test admin.php components
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug test...\n<br>";

try {
    echo "1. Testing config include...\n<br>";
    require_once 'manageone/config.php';
    echo "✓ Config loaded successfully\n<br>";
    
    echo "2. Testing database connection...\n<br>";
    $conn = get_db_connection();
    if ($conn) {
        echo "✓ Database connected successfully\n<br>";
        $conn->close();
    } else {
        echo "✗ Database connection failed\n<br>";
    }
    
    echo "3. Testing review-functions include...\n<br>";
    require_once 'review-functions.php';
    echo "✓ Review functions loaded successfully\n<br>";
    
    echo "4. Testing getReviewStats...\n<br>";
    $reviewStats = getReviewStats();
    echo "✓ Review stats: " . print_r($reviewStats, true) . "\n<br>";
    
    echo "5. Testing getAllReviews...\n<br>";
    $allReviews = getAllReviews();
    echo "✓ All reviews count: " . count($allReviews) . "\n<br>";
    
    echo "6. Testing getPendingReviews...\n<br>";
    $pendingReviews = getPendingReviews();
    echo "✓ Pending reviews count: " . count($pendingReviews) . "\n<br>";
    
    echo "7. Testing getApprovedReviews...\n<br>";
    $approvedReviews = getApprovedReviews(20);
    echo "✓ Approved reviews count: " . count($approvedReviews) . "\n<br>";
    
    echo "\nAll tests passed! The issue might be elsewhere.\n<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "\n<br>";
} catch (Error $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "\n<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "\n<br>";
}
?>
