<?php
// File: review-functions-demo.php
// =================================================================
// Review management functions using SQLite for demo
// =================================================================

require_once 'manageone/config-demo.php';

function storeReview($customerName, $customerEmail, $websiteUrl, $rating, $reviewText, $reportId = null, $sessionId = null) {
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (customer_name, customer_email, website_url, rating, review_text, report_id, session_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([$customerName, $customerEmail, $websiteUrl, $rating, $reviewText, $reportId, $sessionId]);
        
        return $result ? $pdo->lastInsertId() : false;
    } catch (Exception $e) {
        error_log("Error storing review: " . $e->getMessage());
        return false;
    }
}

function getReviewStats() {
    $pdo = get_db_connection();
    if (!$pdo) {
        return ['average' => 4.9, 'count' => 500, 'featured_count' => 0, 'real_count' => 0];
    }
    
    try {
        $stmt = $pdo->query("
            SELECT 
                AVG(rating) as average_rating,
                COUNT(*) as total_count,
                SUM(featured) as featured_count
            FROM reviews 
            WHERE approved = 1
        ");
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['total_count'] > 0) {
            return [
                'average' => round($row['average_rating'], 1),
                'count' => max(500, $row['total_count']), // Show at least 500 for display
                'real_count' => $row['total_count'],
                'featured_count' => $row['featured_count'] ?: 0
            ];
        } else {
            return ['average' => 4.9, 'count' => 500, 'real_count' => 0, 'featured_count' => 0];
        }
    } catch (Exception $e) {
        error_log("Error getting review stats: " . $e->getMessage());
        return ['average' => 4.9, 'count' => 500, 'real_count' => 0, 'featured_count' => 0];
    }
}

function getPendingReviews() {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM reviews WHERE approved = 0 ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting pending reviews: " . $e->getMessage());
        return [];
    }
}

function getApprovedReviews($limit = 10) {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM reviews 
            WHERE approved = 1 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting approved reviews: " . $e->getMessage());
        return [];
    }
}

function updateReviewStatus($reviewId, $approved, $featured) {
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE reviews 
            SET approved = ?, featured = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        
        return $stmt->execute([$approved ? 1 : 0, $featured ? 1 : 0, $reviewId]);
    } catch (Exception $e) {
        error_log("Error updating review status: " . $e->getMessage());
        return false;
    }
}

function deleteReview($reviewId) {
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$reviewId]);
    } catch (Exception $e) {
        error_log("Error deleting review: " . $e->getMessage());
        return false;
    }
}

function toggleReviewApproval($reviewId) {
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        // First get current status
        $stmt = $pdo->prepare("SELECT approved FROM reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $newStatus = $row['approved'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE reviews SET approved = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$newStatus, $reviewId]);
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error toggling review approval: " . $e->getMessage());
        return false;
    }
}

function toggleReviewFeatured($reviewId) {
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        // First get current status
        $stmt = $pdo->prepare("SELECT featured FROM reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $newStatus = $row['featured'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE reviews SET featured = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$newStatus, $reviewId]);
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error toggling review featured status: " . $e->getMessage());
        return false;
    }
}

function hasUserReviewedSession($sessionId) {
    if (empty($sessionId)) return false;
    
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM reviews WHERE session_id = ? LIMIT 1");
        $stmt->execute([$sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (Exception $e) {
        error_log("Error checking if user reviewed: " . $e->getMessage());
        return false;
    }
}

function getFeaturedReviews($limit = 3) {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("
            SELECT customer_name, rating, review_text, created_at 
            FROM reviews 
            WHERE approved = 1 AND featured = 1 
            AND review_text IS NOT NULL AND review_text != ''
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting featured reviews: " . $e->getMessage());
        return [];
    }
}

function getAllReviews($orderBy = 'created_at', $order = 'DESC') {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    
    try {
        $allowedColumns = ['created_at', 'rating', 'customer_name', 'approved', 'featured'];
        $allowedOrder = ['ASC', 'DESC'];
        
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'created_at';
        if (!in_array($order, $allowedOrder)) $order = 'DESC';
        
        $sql = "SELECT * FROM reviews ORDER BY $orderBy $order";
        $stmt = $pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting all reviews: " . $e->getMessage());
        return [];
    }
}

function bulkUpdateReviews($reviewIds, $action, $value = null) {
    if (empty($reviewIds) || !is_array($reviewIds)) return false;
    
    $pdo = get_db_connection();
    if (!$pdo) return false;
    
    try {
        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        
        switch ($action) {
            case 'approve':
                $sql = "UPDATE reviews SET approved = 1, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
                break;
            case 'unapprove':
                $sql = "UPDATE reviews SET approved = 0, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
                break;
            case 'feature':
                $sql = "UPDATE reviews SET featured = 1, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
                break;
            case 'unfeature':
                $sql = "UPDATE reviews SET featured = 0, updated_at = CURRENT_TIMESTAMP WHERE id IN ($placeholders)";
                break;
            case 'delete':
                $sql = "DELETE FROM reviews WHERE id IN ($placeholders)";
                break;
            default:
                return false;
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($reviewIds);
    } catch (Exception $e) {
        error_log("Error bulk updating reviews: " . $e->getMessage());
        return false;
    }
}
?>
