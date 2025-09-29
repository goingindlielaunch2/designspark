<?php
// File: review-functions.php
// =================================================================
// Review management functions using existing ManageOne database
// =================================================================

require_once 'manageone/config.php';

function storeReview($customerName, $customerEmail, $websiteUrl, $rating, $reviewText, $reportId = null, $sessionId = null) {
    $conn = get_db_connection();
    if (!$conn) {
        error_log("storeReview: Failed to get database connection");
        return false;
    }
    
    try {
        error_log("storeReview: Preparing SQL statement");
        $stmt = $conn->prepare("
            INSERT INTO reviews (customer_name, customer_email, website_url, rating, review_text, report_id, session_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        if (!$stmt) {
            error_log("storeReview: Failed to prepare statement - " . $conn->error);
            $conn->close();
            return false;
        }
        
        error_log("storeReview: Binding parameters - rating: $rating");
        $stmt->bind_param("sssisss", $customerName, $customerEmail, $websiteUrl, $rating, $reviewText, $reportId, $sessionId);
        
        error_log("storeReview: Executing statement");
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("storeReview: Execute failed - " . $stmt->error);
            $stmt->close();
            $conn->close();
            return false;
        }
        
        $reviewId = $conn->insert_id;
        error_log("storeReview: Successfully inserted review with ID: " . $reviewId);
        
        $stmt->close();
        $conn->close();
        
        return $reviewId;
    } catch (Exception $e) {
        error_log("Error storing review: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function getReviewStats() {
    $conn = get_db_connection();
    if (!$conn) {
        // Fallback to default values if database is unavailable
        return ['average' => 4.9, 'count' => 500, 'real_count' => 0, 'featured_count' => 0];
    }
    
    try {
        $result = $conn->query("
            SELECT 
                COALESCE(AVG(rating), 4.9) as average_rating,
                COUNT(*) as total_count,
                COALESCE(SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END), 0) as featured_count
            FROM reviews 
            WHERE approved = 1
        ");
        
        if ($result && $row = $result->fetch_assoc()) {
            $returnData = [
                'average' => 4.9,
                'count' => 500,
                'real_count' => 0,
                'featured_count' => 0
            ];
            
            if ($row['total_count'] > 0) {
                $returnData = [
                    'average' => round($row['average_rating'], 1),
                    'count' => (int)$row['total_count'] + 487, // Add base number for social proof
                    'real_count' => (int)$row['total_count'],
                    'featured_count' => (int)$row['featured_count']
                ];
            }
            
            $conn->close();
            return $returnData;
        }
        
        $conn->close();
        // Default fallback if no approved reviews yet
        return ['average' => 4.9, 'count' => 500, 'real_count' => 0, 'featured_count' => 0];
        
    } catch (Exception $e) {
        error_log("Error getting review stats: " . $e->getMessage());
        if ($conn && !$conn->connect_errno) $conn->close();
        return ['average' => 4.9, 'count' => 500, 'real_count' => 0, 'featured_count' => 0];
    }
}

function getApprovedReviews($limit = 10) {
    $conn = get_db_connection();
    if (!$conn) return [];
    
    try {
        $stmt = $conn->prepare("
            SELECT customer_name, rating, review_text, created_at, featured
            FROM reviews 
            WHERE approved = 1 AND review_text IS NOT NULL AND review_text != ''
            ORDER BY featured DESC, created_at DESC 
            LIMIT ?
        ");
        
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        $stmt->close();
        $conn->close();
        
        return $reviews;
    } catch (Exception $e) {
        error_log("Error getting approved reviews: " . $e->getMessage());
        if ($conn) $conn->close();
        return [];
    }
}

function getPendingReviews() {
    $conn = get_db_connection();
    if (!$conn) return [];
    
    try {
        $result = $conn->query("
            SELECT * FROM reviews 
            WHERE approved = 0 
            ORDER BY created_at DESC
        ");
        
        $reviews = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
        
        $conn->close();
        return $reviews;
    } catch (Exception $e) {
        error_log("Error getting pending reviews: " . $e->getMessage());
        if ($conn) $conn->close();
        return [];
    }
}

function updateReviewStatus($reviewId, $approved, $featured = false) {
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("
            UPDATE reviews 
            SET approved = ?, featured = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        $approvedInt = $approved ? 1 : 0;
        $featuredInt = $featured ? 1 : 0;
        
        $stmt->bind_param("iii", $approvedInt, $featuredInt, $reviewId);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Error updating review status: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function hasUserReviewedSession($sessionId) {
    if (empty($sessionId)) return false;
    
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("SELECT id FROM reviews WHERE session_id = ? LIMIT 1");
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $hasReviewed = $result->num_rows > 0;
        
        $stmt->close();
        $conn->close();
        
        return $hasReviewed;
    } catch (Exception $e) {
        error_log("Error checking if user reviewed: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function getFeaturedReviews($limit = 3) {
    $conn = get_db_connection();
    if (!$conn) return [];
    
    try {
        $stmt = $conn->prepare("
            SELECT customer_name, rating, review_text, created_at 
            FROM reviews 
            WHERE approved = 1 AND featured = 1 
            AND review_text IS NOT NULL AND review_text != ''
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        
        if (!$stmt) {
            $conn->close();
            return [];
        }
        
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        $stmt->close();
        $conn->close();
        
        return $reviews;
    } catch (Exception $e) {
        error_log("Error getting featured reviews: " . $e->getMessage());
        if ($conn && !$conn->connect_errno) $conn->close();
        return [];
    }
}

function deleteReview($reviewId) {
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $reviewId);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Error deleting review: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function toggleReviewApproval($reviewId) {
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        // First get current status
        $stmt = $conn->prepare("SELECT approved FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $newStatus = $row['approved'] ? 0 : 1;
            
            $stmt->close();
            
            // Update status
            $stmt = $conn->prepare("UPDATE reviews SET approved = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ii", $newStatus, $reviewId);
            $result = $stmt->execute();
            
            $stmt->close();
            $conn->close();
            
            return $result;
        }
        
        $stmt->close();
        $conn->close();
        return false;
    } catch (Exception $e) {
        error_log("Error toggling review approval: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function toggleReviewFeatured($reviewId) {
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        // First get current status
        $stmt = $conn->prepare("SELECT featured FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $newStatus = $row['featured'] ? 0 : 1;
            
            $stmt->close();
            
            // Update status
            $stmt = $conn->prepare("UPDATE reviews SET featured = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ii", $newStatus, $reviewId);
            $result = $stmt->execute();
            
            $stmt->close();
            $conn->close();
            
            return $result;
        }
        
        $stmt->close();
        $conn->close();
        return false;
    } catch (Exception $e) {
        error_log("Error toggling review featured status: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

function getAllReviews($orderBy = 'created_at', $order = 'DESC') {
    $conn = get_db_connection();
    if (!$conn) return [];
    
    try {
        $allowedColumns = ['created_at', 'rating', 'customer_name', 'approved', 'featured'];
        $allowedOrder = ['ASC', 'DESC'];
        
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'created_at';
        if (!in_array($order, $allowedOrder)) $order = 'DESC';
        
        $sql = "SELECT * FROM reviews ORDER BY $orderBy $order";
        $result = $conn->query($sql);
        
        $reviews = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
        
        $conn->close();
        return $reviews;
    } catch (Exception $e) {
        error_log("Error getting all reviews: " . $e->getMessage());
        if ($conn) $conn->close();
        return [];
    }
}

function bulkUpdateReviews($reviewIds, $action, $value = null) {
    if (empty($reviewIds) || !is_array($reviewIds)) return false;
    
    $conn = get_db_connection();
    if (!$conn) return false;
    
    try {
        $placeholders = str_repeat('?,', count($reviewIds) - 1) . '?';
        
        switch ($action) {
            case 'approve':
                $sql = "UPDATE reviews SET approved = 1, updated_at = NOW() WHERE id IN ($placeholders)";
                break;
            case 'unapprove':
                $sql = "UPDATE reviews SET approved = 0, updated_at = NOW() WHERE id IN ($placeholders)";
                break;
            case 'feature':
                $sql = "UPDATE reviews SET featured = 1, updated_at = NOW() WHERE id IN ($placeholders)";
                break;
            case 'unfeature':
                $sql = "UPDATE reviews SET featured = 0, updated_at = NOW() WHERE id IN ($placeholders)";
                break;
            case 'delete':
                $sql = "DELETE FROM reviews WHERE id IN ($placeholders)";
                break;
            default:
                $conn->close();
                return false;
        }
        
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($reviewIds));
        $stmt->bind_param($types, ...$reviewIds);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("Error bulk updating reviews: " . $e->getMessage());
        if ($conn) $conn->close();
        return false;
    }
}

?>
