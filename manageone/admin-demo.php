<?php
// File: admin-demo.php
// =================================================================
// Demo admin interface using SQLite database
// =================================================================

require_once 'config-demo.php';
require_once '../review-functions-demo.php';

// --- LOGIN HANDLING ---
$logged_in = false;
$login_error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $logged_in = true;
    } else {
        $login_error = 'Invalid username or password';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    session_start();
}

$logged_in = $_SESSION['admin_logged_in'] ?? false;

// --- HANDLE ACTIONS AFTER LOGIN ---
if ($logged_in) {
    $page = $_GET['page'] ?? 'dashboard';
    
    // --- Data for Reviews Page ---
    if ($page === 'reviews') {
        // Handle individual review actions
        if ($_POST['action'] ?? '' === 'update_review') {
            $reviewId = (int)$_POST['review_id'];
            $approved = $_POST['status'] === 'approve';
            $featured = isset($_POST['featured']);
            
            if (updateReviewStatus($reviewId, $approved, $featured)) {
                $_SESSION['feedback_message'] = ['type' => 'success', 'text' => $approved ? 'Review approved successfully!' : 'Review rejected successfully!'];
            } else {
                $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Failed to update review status'];
            }
            
            // Redirect to prevent form resubmission
            header('Location: admin-demo.php?page=reviews');
            exit;
        }
        
        // Handle individual management actions
        if ($_POST['action'] ?? '' === 'manage_review') {
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
            header('Location: admin-demo.php?page=reviews');
            exit;
        }
        
        // Handle bulk actions
        if ($_POST['action'] ?? '' === 'bulk_action') {
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
            header('Location: admin-demo.php?page=reviews');
            exit;
        }
        
        // Get review data
        $orderBy = $_GET['order_by'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        $allReviews = getAllReviews($orderBy, $order);
        $pendingReviews = getPendingReviews();
        $approvedReviews = getApprovedReviews(20);
        $reviewStats = getReviewStats();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $logged_in ? 'Admin Dashboard - Demo' : 'Admin Login - Demo'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4'
                    }
                }
            }
        }
    </script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
        
        body {
            font-family: "Inter", sans-serif;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">

<?php if (!$logged_in): ?>
<!-- Login Form -->
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Admin Login - Demo
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Demo credentials: admin@demo.com / password123
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <input id="username" name="username" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address" value="admin@demo.com">
                </div>
                <div>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Password" value="password123">
                </div>
            </div>

            <?php if ($login_error): ?>
            <div class="text-red-600 text-sm text-center"><?php echo $login_error; ?></div>
            <?php endif; ?>

            <div>
                <button type="submit" name="login" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Admin Dashboard -->
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Admin Dashboard - Demo</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="admin-demo.php?page=reviews" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium <?php echo $page === 'reviews' ? 'bg-gray-200' : ''; ?>">
                        Reviews
                    </a>
                    <form method="POST" class="inline">
                        <button type="submit" name="logout" class="text-red-600 hover:text-red-800 px-3 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php
        // Display feedback messages
        if (isset($_SESSION['feedback_message'])) {
            $message = $_SESSION['feedback_message'];
            $bgColor = $message['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
            echo "<div class='$bgColor border px-4 py-3 rounded mb-4'>{$message['text']}</div>";
            unset($_SESSION['feedback_message']);
        }
        ?>

        <?php if ($page === 'reviews'): ?>
            <!-- Include the reviews content -->
            <div class="px-4 py-6 sm:px-0">
                <?php include 'partials/reviews_content.php'; ?>
            </div>
        <?php else: ?>
            <!-- Dashboard Home -->
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to the Demo Admin</h2>
                    <p class="text-gray-600 mb-4">This is a demo version of the admin interface using SQLite database.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Review Management</h3>
                            <p class="text-blue-700 mb-3">Manage customer reviews, approve/reject submissions, and feature the best reviews.</p>
                            <a href="admin-demo.php?page=reviews" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                Go to Reviews →
                            </a>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-green-900 mb-2">Demo Features</h3>
                            <p class="text-green-700 mb-3">This demo includes sample reviews and all management functionality.</p>
                            <a href="../add-sample-reviews-demo.php" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-800">
                                Add Sample Data →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Bulk action functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            reviewCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    reviewCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.review-checkbox:checked').length;
        if (selectedCount) {
            selectedCount.textContent = checked + ' selected';
        }
    }
    
    // Initial count
    updateSelectedCount();
});

function confirmBulkAction() {
    const checked = document.querySelectorAll('.review-checkbox:checked').length;
    const action = document.querySelector('select[name="bulk_action_type"]').value;
    
    if (checked === 0) {
        alert('Please select at least one review.');
        return false;
    }
    
    if (!action) {
        alert('Please select an action.');
        return false;
    }
    
    return confirm(`Are you sure you want to ${action} ${checked} review(s)?`);
}
</script>

<?php endif; ?>
</body>
</html>
