<?php
// File: admin.php (Version 6)
// =================================================================
// Main dashboard with logic to handle the new edit page.
// =================================================================

// Start session first before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once '../review-functions.php';

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// --- Page & Feedback Handling ---
$page = $_GET['page'] ?? 'dashboard'; // Default page is 'dashboard'
$feedback_message = $_SESSION['feedback_message'] ?? null;
unset($_SESSION['feedback_message']); // Clear message after displaying

$conn = get_db_connection();

// --- Data Fetching for Dashboard page ---
if ($page === 'dashboard') {
    // Handle bulk delete actions for dashboard items
    if ($_POST['action'] ?? '' === 'bulk_delete') {
        $item_type = $_POST['item_type'] ?? '';
        $item_ids = $_POST['item_ids'] ?? [];
        
        if (!empty($item_ids) && in_array($item_type, ['evaluations', 'contacts'])) {
            $item_ids = array_map('intval', $item_ids);
            $placeholders = str_repeat('?,', count($item_ids) - 1) . '?';
            
            $success = false;
            $count = count($item_ids);
            
            if ($item_type === 'evaluations') {
                $stmt = $conn->prepare("DELETE FROM evaluations WHERE id IN ($placeholders)");
                $stmt->bind_param(str_repeat('i', $count), ...$item_ids);
                $success = $stmt->execute();
                $item_name = 'evaluation' . ($count > 1 ? 's' : '');
            } else if ($item_type === 'contacts') {
                $stmt = $conn->prepare("DELETE FROM contacts WHERE id IN ($placeholders)");
                $stmt->bind_param(str_repeat('i', $count), ...$item_ids);
                $success = $stmt->execute();
                $item_name = 'contact' . ($count > 1 ? 's' : '');
            }
            
            if ($success) {
                $_SESSION['feedback_message'] = ['type' => 'success', 'text' => "Successfully deleted $count $item_name!"];
            } else {
                $_SESSION['feedback_message'] = ['type' => 'error', 'text' => "Failed to delete $item_name"];
            }
        } else {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Please select items to delete'];
        }
        
        // Redirect to prevent form resubmission
        header('Location: admin.php?page=dashboard');
        exit;
    }

    $search_term = '';
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = $conn->real_escape_string($_GET['search']);
    }

    $evaluations_sql = "SELECT id, name, email, website_url, subscribed_newsletter, submission_date FROM evaluations";
    if ($search_term) $evaluations_sql .= " WHERE name LIKE '%$search_term%' OR email LIKE '%$search_term%' OR website_url LIKE '%$search_term%'";
    $evaluations_sql .= " ORDER BY submission_date DESC";
    $evaluations_result = $conn->query($evaluations_sql);
    $evaluations_count = $evaluations_result->num_rows;

    $contacts_sql = "SELECT id, name, email, subject, message, submission_date FROM contacts";
    if ($search_term) $contacts_sql .= " WHERE name LIKE '%$search_term%' OR email LIKE '%$search_term%' OR subject LIKE '%$search_term%'";
    $contacts_sql .= " ORDER BY submission_date DESC";
    $contacts_result = $conn->query($contacts_sql);
    $contacts_count = $contacts_result->num_rows;
}

// --- Data for Messaging Page ---
if ($page === 'messages') {
    $newsletter_subscribers_count_result = $conn->query("SELECT COUNT(id) as count FROM evaluations WHERE subscribed_newsletter = 1");
    $newsletter_subscribers_count = $newsletter_subscribers_count_result->fetch_assoc()['count'];
}

// --- Data for Reviews Page ---
if ($page === 'reviews') {
    // Generate unique submission ID for tracking
    $submission_id = uniqid('submit_', true);
    
    // Current timestamp debugging
    if (!empty($_POST)) {
        error_log("=== SUBMISSION $submission_id " . date('Y-m-d H:i:s') . " ===");
        error_log("Action: " . ($_POST['action'] ?? 'NO ACTION'));
        error_log("Form ID: " . ($_POST['form_id'] ?? 'NO FORM ID'));
        error_log("Status field: " . ($_POST['status'] ?? 'NO STATUS'));
        error_log("Review ID field: " . ($_POST['review_id'] ?? 'NO REVIEW ID'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET'));
    }
    
    // Handle individual review actions
    $action = $_POST['action'] ?? '';
    $handler_executed = false; // Safety flag to prevent multiple handlers
    
    error_log("EXPLICIT DEBUG: action variable = '" . $action . "'");
    error_log("EXPLICIT DEBUG: action length = " . strlen($action));
    error_log("EXPLICIT DEBUG: action hex = " . bin2hex($action));
    error_log("EXPLICIT DEBUG: action === 'update_review' ? " . ($action === 'update_review' ? 'TRUE' : 'FALSE'));
    error_log("EXPLICIT DEBUG: action === 'add_review' ? " . ($action === 'add_review' ? 'TRUE' : 'FALSE'));
    
    if ($action === 'update_review' && !$handler_executed) {
        $handler_executed = true;
        
        // Safety check: update_review requires review_id and status
        if (!isset($_POST['review_id']) || !isset($_POST['status'])) {
            error_log("[$submission_id] ERROR: update_review called without required fields");
            error_log("[$submission_id] review_id present: " . (isset($_POST['review_id']) ? 'YES' : 'NO'));
            error_log("[$submission_id] status present: " . (isset($_POST['status']) ? 'YES' : 'NO'));
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Invalid update request - missing required fields'];
            header('Location: admin.php?page=reviews');
            exit;
        }
        error_log("[$submission_id] UPDATE action triggered at " . date('Y-m-d H:i:s'));
        $reviewId = (int)$_POST['review_id'];
        $approved = $_POST['status'] === 'approve';
        $featured = isset($_POST['featured']);
        
        if (updateReviewStatus($reviewId, $approved, $featured)) {
            $_SESSION['feedback_message'] = ['type' => 'success', 'text' => $approved ? 'Review approved successfully!' : 'Review rejected successfully!'];
        } else {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Failed to update review status'];
        }
        
        error_log("[$submission_id] UPDATE action completed, redirecting");
        // Redirect to prevent form resubmission
        header('Location: admin.php?page=reviews');
        exit;
    }
    
    // Handle adding new review
    if ($action === 'add_review' && !$handler_executed) {
        $handler_executed = true;
        error_log("[$submission_id] ADD action triggered at " . date('Y-m-d H:i:s'));
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerEmail = trim($_POST['customer_email'] ?? '');
        $websiteUrl = trim($_POST['website_url'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $reviewText = trim($_POST['review_text'] ?? '');
        $approved = isset($_POST['approved']) && $_POST['approved'] === '1';
        $featured = isset($_POST['featured']) && $_POST['featured'] === '1';
        
        error_log("[$submission_id] approved checkbox = " . (isset($_POST['approved']) ? $_POST['approved'] : 'NOT SET'));
        error_log("[$submission_id] featured checkbox = " . (isset($_POST['featured']) ? $_POST['featured'] : 'NOT SET'));
        error_log("[$submission_id] processed approved = " . ($approved ? 'TRUE' : 'FALSE'));
        error_log("[$submission_id] processed featured = " . ($featured ? 'TRUE' : 'FALSE'));
        
        // Validate required fields
        if (empty($customerName) || empty($reviewText) || $rating < 1 || $rating > 5) {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Please fill in all required fields with valid data'];
        } else {
            // Store the review
            $reviewId = storeReview($customerName, $customerEmail, $websiteUrl, $rating, $reviewText);
            
            if ($reviewId) {
                // Update status if needed - but do this BEFORE setting success message
                if ($approved || $featured) {
                    $statusResult = updateReviewStatus($reviewId, $approved, $featured);
                    
                    if (!$statusResult) {
                        error_log("Add Review: Status update failed, but review was stored");
                    }
                }
                
                // Set success message regardless of status update result
                $statusText = '';
                if ($approved && $featured) {
                    $statusText = ' (approved and featured)';
                } elseif ($approved) {
                    $statusText = ' (approved)';
                } elseif ($featured) {
                    $statusText = ' (featured only)';
                } else {
                    $statusText = ' (pending approval)';
                }
                
                $_SESSION['feedback_message'] = ['type' => 'success', 'text' => "New review added successfully$statusText!"];
                error_log("[$submission_id] ADD action success message set");
            } else {
                $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Failed to add review - check error logs for details'];
                error_log("[$submission_id] ADD action failed - storeReview returned false");
            }
        }
        
        error_log("[$submission_id] ADD action completed, redirecting");
        // Redirect to prevent form resubmission
        header('Location: admin.php?page=reviews');
        exit;
    }
    
    // Handle individual management actions
    if ($_POST['action'] ?? '' === 'manage_review' && !$handler_executed) {
        $handler_executed = true;
        $reviewId = (int)$_POST['review_id'];
        $managementAction = $_POST['management_action'] ?? '';
        
        $success = false;
        $message = '';
        
        switch ($managementAction) {
            case 'delete':
                $success = deleteReview($reviewId);
                $message = $success ? 'Review deleted successfully!' : 'Failed to delete review';
                break;
            case 'toggle_approval':
                $success = toggleReviewApproval($reviewId);
                $message = $success ? 'Review approval status updated!' : 'Failed to update approval status';
                break;
            case 'toggle_featured':
                $success = toggleReviewFeatured($reviewId);
                $message = $success ? 'Review featured status updated!' : 'Failed to update featured status';
                break;
        }
        
        $_SESSION['feedback_message'] = ['type' => $success ? 'success' : 'error', 'text' => $message];
        
        // Redirect to prevent form resubmission
        header('Location: admin.php?page=reviews');
        exit;
    }
    
    // Handle bulk actions
    if ($_POST['action'] ?? '' === 'bulk_action' && !$handler_executed) {
        $handler_executed = true;
        $reviewIds = $_POST['review_ids'] ?? [];
        $bulkAction = $_POST['bulk_action_type'] ?? '';
        
        if (!empty($reviewIds) && !empty($bulkAction)) {
            $reviewIds = array_map('intval', $reviewIds);
            $success = bulkUpdateReviews($reviewIds, $bulkAction);
            
            $count = count($reviewIds);
            $actions = [
                'approve' => "approved $count reviews",
                'unapprove' => "unapproved $count reviews",
                'feature' => "featured $count reviews",
                'unfeature' => "unfeatured $count reviews",
                'delete' => "deleted $count reviews"
            ];
            
            $actionText = $actions[$bulkAction] ?? 'updated reviews';
            $message = $success ? "Successfully $actionText!" : "Failed to $actionText";
            
            $_SESSION['feedback_message'] = ['type' => $success ? 'success' : 'error', 'text' => $message];
        } else {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Please select reviews and an action'];
        }
        
        // Redirect to prevent form resubmission
        header('Location: admin.php?page=reviews');
        exit;
    }
    
    // Debug: If we reach here without a handler executing and POST is not empty
    if (!empty($_POST) && !$handler_executed) {
        error_log("[$submission_id] WARNING: POST request but no handler executed!");
        error_log("[$submission_id] Action was: '" . $action . "'");
    }
    
    // Get review data
    $orderBy = $_GET['order_by'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';
    $allReviews = getAllReviews($orderBy, $order);
    $pendingReviews = getPendingReviews();
    $approvedReviews = getApprovedReviews(20);
    $reviewStats = getReviewStats();
}

// --- Data for Edit Page ---
if ($page === 'edit') {
    $edit_id = $_GET['id'] ?? 0;
    $edit_type = $_GET['type'] ?? '';
    $entry_data = null;

    if ($edit_id > 0 && $edit_type === 'evaluation') {
        $stmt = $conn->prepare("SELECT * FROM evaluations WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $entry_data = $result->fetch_assoc();
        }
        $stmt->close();
    }
    
    // If no valid data is found, redirect to dashboard to prevent errors
    if ($entry_data === null) {
        $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'The requested entry could not be found.'];
        header('Location: admin.php?page=dashboard');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-icon { width: 1.5rem; height: 1.5rem; }
        .active-nav { background-color: #3b82f6; color: white; }
        
        /* Mobile sidebar animations */
        .mobile-sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Hide sidebar on mobile by default */
        @media (max-width: 767px) {
            .mobile-sidebar.hidden-mobile {
                transform: translateX(-100%);
            }
            
            .mobile-sidebar.visible-mobile {
                transform: translateX(0);
            }
        }
        
        /* Always show sidebar on desktop */
        @media (min-width: 768px) {
            .mobile-sidebar {
                transform: translateX(0) !important;
            }
        }
        
        /* Overlay for mobile sidebar */
        .mobile-overlay {
            transition: opacity 0.3s ease-in-out;
        }
        
        /* Hide overlay by default and only show on mobile when sidebar is open */
        .mobile-overlay.hidden-mobile {
            opacity: 0;
            pointer-events: none;
        }
        
        .mobile-overlay.visible-mobile {
            opacity: 1;
            pointer-events: all;
        }
        
        /* Never show overlay on desktop */
        @media (min-width: 768px) {
            .mobile-overlay {
                display: none !important;
            }
        }
        
        /* Navigation text animations for mobile */
        .nav-text {
            transition: opacity 0.2s ease-in-out, width 0.2s ease-in-out;
        }
        
        @media (max-width: 768px) {
            .nav-text.collapsed {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen bg-gray-100">
    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden mobile-overlay hidden-mobile" onclick="toggleMobileSidebar()"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="fixed md:relative z-30 md:z-auto flex flex-col w-64 bg-gray-800 h-full mobile-sidebar hidden-mobile md:block md:translate-x-0">
        <div class="flex items-center justify-center h-16 bg-gray-900">
            <span class="text-white font-bold uppercase">Admin Panel</span>
        </div>
        <div class="flex flex-col flex-1 overflow-y-auto">
            <nav class="flex-1 px-2 py-4 bg-gray-800">
                <a href="admin.php?page=dashboard" class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 rounded-md <?php if($page === 'dashboard') echo 'active-nav'; ?>" onclick="closeMobileSidebar()">
                    <svg class="sidebar-icon mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="admin.php?page=messages" class="flex items-center px-4 py-2 mt-2 text-gray-100 hover:bg-gray-700 rounded-md <?php if($page === 'messages') echo 'active-nav'; ?>" onclick="closeMobileSidebar()">
                    <svg class="sidebar-icon mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="nav-text">Messages</span>
                </a>
                <a href="admin.php?page=reviews" class="flex items-center px-4 py-2 mt-2 text-gray-100 hover:bg-gray-700 rounded-md <?php if($page === 'reviews') echo 'active-nav'; ?>" onclick="closeMobileSidebar()">
                    <svg class="sidebar-icon mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span class="nav-text">Reviews</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2 mt-2 text-gray-400 hover:bg-gray-700 rounded-md">
                    <svg class="sidebar-icon mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
                    <span class="nav-text">Analytics</span>
                    <span class="text-xs ml-2 bg-gray-600 px-2 py-1 rounded-full nav-text">Soon</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2 mt-2 text-gray-400 hover:bg-gray-700 rounded-md">
                    <svg class="sidebar-icon mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="nav-text">Settings</span>
                    <span class="text-xs ml-2 bg-gray-600 px-2 py-1 rounded-full nav-text">Soon</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        <div class="flex items-center justify-between h-16 bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="flex items-center px-4">
                <!-- Mobile hamburger button -->
                <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" onclick="toggleMobileSidebar()">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <!-- Page title for mobile -->
                <h1 class="ml-4 text-lg font-semibold text-gray-900 md:hidden">
                    <?php 
                    switch($page) {
                        case 'dashboard': echo 'Dashboard'; break;
                        case 'messages': echo 'Messages'; break;
                        case 'reviews': echo 'Reviews'; break;
                        case 'edit': echo 'Edit Entry'; break;
                        default: echo 'Admin Panel'; break;
                    }
                    ?>
                </h1>
            </div>
            <div class="flex items-center pr-4">
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">Logout</a>
            </div>
        </div>
        <div class="p-4 md:p-8">
            <?php if ($feedback_message): ?>
                <div class="mb-4 p-4 rounded-md <?php echo $feedback_message['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo htmlspecialchars($feedback_message['text']); ?>
                </div>
            <?php endif; ?>

            <?php if ($page === 'dashboard'): ?>
                <?php include 'partials/dashboard_content.php'; ?>
            <?php elseif ($page === 'messages'): ?>
                <?php include 'partials/messages_content.php'; ?>
            <?php elseif ($page === 'reviews'): ?>
                <?php include 'partials/reviews_content.php'; ?>
            <?php elseif ($page === 'edit' && $edit_type === 'evaluation'): ?>
                <?php include 'partials/edit_entry_content.php'; ?>
            <?php else: ?>
                <h1 class="text-2xl font-bold">Page Not Found</h1>
                <p>The page you are looking for does not exist.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Mobile sidebar functionality
let isMobileSidebarOpen = false;

function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    if (isMobileSidebarOpen) {
        closeMobileSidebar();
    } else {
        openMobileSidebar();
    }
}

function openMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar.classList.remove('hidden-mobile');
    sidebar.classList.add('visible-mobile');
    overlay.classList.remove('hidden-mobile');
    overlay.classList.add('visible-mobile');
    
    isMobileSidebarOpen = true;
    
    // Prevent body scroll when sidebar is open
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');
    
    sidebar.classList.remove('visible-mobile');
    sidebar.classList.add('hidden-mobile');
    overlay.classList.remove('visible-mobile');
    overlay.classList.add('hidden-mobile');
    
    isMobileSidebarOpen = false;
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close sidebar when clicking outside or on navigation links
document.addEventListener('DOMContentLoaded', function() {
    // Close sidebar when pressing escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobileSidebarOpen) {
            closeMobileSidebar();
        }
    });
    
    // Handle window resize - close mobile sidebar if window becomes desktop size
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && isMobileSidebarOpen) {
            closeMobileSidebar();
        }
    });
    
    // Touch handling for mobile swipe gestures
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        // Swipe right to open sidebar (only if near left edge)
        if (swipeDistance > swipeThreshold && touchStartX < 50 && !isMobileSidebarOpen) {
            openMobileSidebar();
        }
        
        // Swipe left to close sidebar
        if (swipeDistance < -swipeThreshold && isMobileSidebarOpen) {
            closeMobileSidebar();
        }
    }
});
</script>

</body>
</html>
<?php
$conn->close();
?>
