<?php
// File: submit-review.php
// =================================================================
// Handle review submissions from the advanced report page
// =================================================================

require_once 'review-functions.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and sanitize form data
// Support both original form fields and new standalone form fields
$customerName = trim($_POST['customer_name'] ?? $_POST['name'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? $_POST['email'] ?? '');
$websiteUrl = trim($_POST['website_url'] ?? $_POST['website'] ?? '');
$rating = (int)($_POST['rating'] ?? 0);
$reviewText = trim($_POST['review_text'] ?? '');
$reportId = trim($_POST['report_id'] ?? '');
$sessionId = trim($_POST['session_id'] ?? '');
$permission = isset($_POST['permission']) ? (bool)$_POST['permission'] : false;

// Basic validation
if (empty($customerName)) {
    echo json_encode(['success' => false, 'message' => 'Customer name is required']);
    exit;
}

if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Valid email address is required']);
    exit;
}

if (empty($websiteUrl)) {
    // Website URL is optional for standalone reviews
    $websiteUrl = '';
} elseif (!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid website URL or leave it blank']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please select a rating between 1 and 5 stars']);
    exit;
}

if (empty($reviewText)) {
    echo json_encode(['success' => false, 'message' => 'Please write a review']);
    exit;
}

// Check if user has already reviewed this session (only if session ID provided)
if (!empty($sessionId) && hasUserReviewedSession($sessionId)) {
    echo json_encode(['success' => false, 'message' => 'You have already submitted a review for this report']);
    exit;
}

try {
    // Store the review
    $reviewId = storeReview($customerName, $customerEmail, $websiteUrl, $rating, $reviewText, $reportId, $sessionId);
    
    if ($reviewId) {
        // If permission was given and it's a high rating, consider featuring it
        if ($permission && $rating >= 4) {
            updateReviewStatus($reviewId, true, true); // Approve and feature
        } elseif ($permission) {
            updateReviewStatus($reviewId, true, false); // Approve but don't feature
        }
        // If no permission given, leave as pending for manual review
        
        // Log successful review submission
        $permissionText = $permission ? ' (with display permission)' : ' (pending approval)';
        error_log("DesignSpark Review Submitted: Rating {$rating}/5 from {$customerEmail} for " . ($websiteUrl ?: 'N/A') . " (Review ID: {$reviewId}){$permissionText}");
        
        echo json_encode([
            'success' => true, 
            'review_id' => $reviewId,
            'message' => $permission ? 
                'Thank you for your review! It will appear on our website after a brief review.' :
                'Thank you for your review! It has been submitted for approval.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to store review. Please try again.']);
    }
} catch (Exception $e) {
    error_log("Review submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
}
?>
