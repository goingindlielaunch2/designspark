<?php
// File: add-sample-reviews-demo.php
// =================================================================
// Add sample reviews for demo purposes using SQLite
// =================================================================

require_once 'review-functions-demo.php';

echo "Adding sample reviews for demo...\n";

// Sample reviews data
$sampleReviews = [
    [
        'name' => 'Sarah Johnson',
        'email' => 'sarah@techstartup.com',
        'website' => 'https://techstartup.com',
        'rating' => 5,
        'text' => 'This analysis was incredibly detailed and helpful. The insights about our website\'s performance were spot-on, and the recommendations have already improved our conversion rates significantly!',
        'approved' => 1,
        'featured' => 1
    ],
    [
        'name' => 'Michael Chen',
        'email' => 'mike@digitalagency.com',
        'website' => 'https://digitalagency.com',
        'rating' => 5,
        'text' => 'Outstanding service! The report provided actionable insights that we implemented immediately. Our client satisfaction has improved dramatically since following the recommendations.',
        'approved' => 1,
        'featured' => 1
    ],
    [
        'name' => 'Emma Rodriguez',
        'email' => 'emma@boutique.com',
        'website' => 'https://boutique.com',
        'rating' => 4,
        'text' => 'Great analysis of our e-commerce site. The UX recommendations were particularly valuable. Would definitely recommend this service to other business owners.',
        'approved' => 1,
        'featured' => 1
    ],
    [
        'name' => 'David Thompson',
        'email' => 'david@consulting.com',
        'website' => 'https://consulting.com',
        'rating' => 5,
        'text' => 'The most comprehensive website analysis I\'ve ever received. Every aspect was covered in detail, and the presentation was professional and easy to understand.',
        'approved' => 1,
        'featured' => 0
    ],
    [
        'name' => 'Lisa Park',
        'email' => 'lisa@restaurant.com',
        'website' => 'https://restaurant.com',
        'rating' => 4,
        'text' => 'Helped us identify several issues with our online ordering system. The suggestions for improvement were practical and within our budget to implement.',
        'approved' => 1,
        'featured' => 0
    ],
    [
        'name' => 'Alex Rivera',
        'email' => 'alex@nonprofit.org',
        'website' => 'https://nonprofit.org',
        'rating' => 5,
        'text' => 'Excellent service that provided valuable insights for our nonprofit website. The team understood our unique needs and provided tailored recommendations.',
        'approved' => 0,
        'featured' => 0
    ],
    [
        'name' => 'Jennifer Adams',
        'email' => 'jen@fitness.com',
        'website' => 'https://fitness.com',
        'rating' => 3,
        'text' => 'Good analysis overall. Some recommendations were helpful, though I felt some areas could have been covered in more depth.',
        'approved' => 0,
        'featured' => 0
    ],
    [
        'name' => 'Robert Kim',
        'email' => 'robert@lawfirm.com',
        'website' => 'https://lawfirm.com',
        'rating' => 5,
        'text' => 'Professional, thorough, and delivered exactly what was promised. The security audit portion was particularly valuable for our law firm.',
        'approved' => 1,
        'featured' => 0
    ]
];

$successCount = 0;
$totalCount = count($sampleReviews);

foreach ($sampleReviews as $review) {
    $reportId = 'demo_' . uniqid();
    $sessionId = 'demo_session_' . uniqid();
    
    $reviewId = storeReview(
        $review['name'],
        $review['email'],
        $review['website'],
        $review['rating'],
        $review['text'],
        $reportId,
        $sessionId
    );
    
    if ($reviewId) {
        // Update approval and featured status
        updateReviewStatus($reviewId, $review['approved'], $review['featured']);
        $successCount++;
        echo "✓ Added review from {$review['name']} (ID: $reviewId)\n";
    } else {
        echo "✗ Failed to add review from {$review['name']}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Successfully added $successCount out of $totalCount sample reviews\n";

// Show current stats
$stats = getReviewStats();
echo "\nCurrent Review Stats:\n";
echo "- Average Rating: {$stats['average']}/5\n";
echo "- Total Reviews: {$stats['real_count']}\n";
echo "- Featured Reviews: {$stats['featured_count']}\n";

echo "\nDemo reviews added successfully! You can now test the admin interface.\n";
?>
