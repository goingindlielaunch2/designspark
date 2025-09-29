<?php
// Test script to add sample featured reviews for demonstration
require_once 'review-functions.php';

echo "Adding sample featured reviews...\n\n";

// Sample reviews data
$sampleReviews = [
    [
        'name' => 'Sarah Johnson',
        'email' => 'sarah@techstartup.com',
        'website' => 'https://techstartup.com',
        'rating' => 5,
        'text' => 'This analysis was incredibly detailed and gave me actionable insights I could implement immediately. The UX recommendations helped increase our conversion rate by 23% within just two weeks!',
        'featured' => true
    ],
    [
        'name' => 'Mike Chen',
        'email' => 'mike@ecommerce.com', 
        'website' => 'https://ecommerce.com',
        'rating' => 5,
        'text' => 'Best investment I made for my website this year. The report identified issues I never noticed and provided clear, specific solutions. My bounce rate dropped by 35%.',
        'featured' => true
    ],
    [
        'name' => 'Lisa Rodriguez',
        'email' => 'lisa@consultancy.com',
        'website' => 'https://consultancy.com', 
        'rating' => 4,
        'text' => 'Very thorough analysis with practical recommendations. The mobile optimization suggestions alone were worth the price. Helped me prioritize what to fix first.',
        'featured' => false
    ],
    [
        'name' => 'David Park',
        'email' => 'david@agency.com',
        'website' => 'https://agency.com',
        'rating' => 5,
        'text' => 'Outstanding service! The detailed breakdown of each page element and specific improvement suggestions made it easy for our dev team to implement changes.',
        'featured' => true
    ],
    [
        'name' => 'Emma Wilson',
        'email' => 'emma@portfolio.com',
        'website' => 'https://portfolio.com',
        'rating' => 4,
        'text' => 'Great insights into user experience issues I had overlooked. The loading speed analysis was particularly helpful.',
        'featured' => false
    ]
];

foreach ($sampleReviews as $review) {
    // Store review
    $reviewId = storeReview(
        $review['name'],
        $review['email'], 
        $review['website'],
        $review['rating'],
        $review['text'],
        'sample_' . uniqid(),
        'sample_session_' . uniqid()
    );
    
    if ($reviewId) {
        // Approve and set featured status
        $success = updateReviewStatus($reviewId, true, $review['featured']);
        
        $status = $success ? 'SUCCESS' : 'FAILED';
        $featured = $review['featured'] ? ' (FEATURED)' : '';
        
        echo "[$status] Added review from {$review['name']} - {$review['rating']}/5{$featured}\n";
    } else {
        echo "[FAILED] Could not add review from {$review['name']}\n";
    }
}

// Show stats
echo "\n" . str_repeat('-', 50) . "\n";
echo "REVIEW STATS:\n";
$stats = getReviewStats();
echo "- Average Rating: {$stats['average']}/5\n";
echo "- Total Reviews: {$stats['real_count']}\n";  
echo "- Featured Reviews: {$stats['featured_count']}\n";
echo "- Public Display Count: {$stats['count']}+\n";

echo "\nFeatured reviews for homepage:\n";
$featured = getFeaturedReviews(5);
foreach ($featured as $i => $review) {
    echo ($i + 1) . ". {$review['customer_name']} - {$review['rating']}/5\n";
    echo "   \"" . substr($review['review_text'], 0, 80) . "...\"\n";
}

echo "\nSample reviews added successfully! ✅\n";
echo "Visit your admin panel to see them: /manageone/admin.php?page=reviews\n";
echo "Check your homepage to see featured reviews: /index.php\n";
?>
