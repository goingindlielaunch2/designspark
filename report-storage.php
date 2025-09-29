<?php
// File: report-storage.php
// =================================================================
// Simple file-based storage for AI reports (can be replaced with database later)
// =================================================================

// =================================================================
// 🗂️ REPORT CLEANUP SETTINGS - Easy Configuration (CURRENTLY DISABLED)
// =================================================================

// Uncomment the lines below to enable automatic cleanup
// $cleanupSettings = [
//     'enabled' => true,        // ✅ Turn cleanup on/off
//     'days' => 365,           // 📅 Delete reports older than X days (365 = 1 year)
//     'chance' => 10,          // 🎲 Run cleanup X% of the time (1-100)
//     'log' => true            // 📝 Log cleanup activity
// ];

// Convert to constants for easy access (CURRENTLY DISABLED)
// define('CLEANUP_ENABLED', $cleanupSettings['enabled']);
// define('CLEANUP_DAYS', $cleanupSettings['days']);
// define('CLEANUP_CHANCE', $cleanupSettings['chance']);
// define('CLEANUP_LOG', $cleanupSettings['log']);

// Temporary constants while cleanup is disabled
define('CLEANUP_ENABLED', false);
define('CLEANUP_DAYS', 365);
define('CLEANUP_CHANCE', 10);
define('CLEANUP_LOG', true);

// =================================================================
// Cleanup Function (READY TO USE - just enable above settings)
// =================================================================

function cleanupOldReports($daysOld = CLEANUP_DAYS) {
    if (!CLEANUP_ENABLED) {
        return; // Exit early if cleanup is disabled
    }
    
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) return;
    
    $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
    $files = glob($reportsDir . '/*.json');
    $deletedCount = 0;
    $totalFiles = count($files);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoffTime) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
    
    // Optional: Log cleanup activity
    if (CLEANUP_LOG && $deletedCount > 0) {
        error_log("DesignSpark Report Cleanup: Deleted $deletedCount old reports out of $totalFiles total files");
    }
    
    return "Cleanup complete: Deleted $deletedCount old reports out of $totalFiles total files.";
}

// =================================================================
// Main Storage Functions
// =================================================================

function storeReportData($websiteUrl, $reportData) {
    // TO ENABLE CLEANUP: Uncomment the lines below
    // Run cleanup occasionally (based on CLEANUP_CHANCE setting)
    // if (CLEANUP_ENABLED && rand(1, 100) <= CLEANUP_CHANCE) {
    //     cleanupOldReports();
    // }
    
    // Create a unique report ID based on website URL and timestamp
    $reportId = md5($websiteUrl . time());
    
    // Ensure reports directory exists
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
    // Add metadata to report data
    $reportData['website_url'] = $websiteUrl;
    $reportData['created_at'] = date('Y-m-d H:i:s');
    
    // Store the report data
    $filePath = $reportsDir . '/' . $reportId . '.json';
    file_put_contents($filePath, json_encode($reportData));
    
    return $reportId;
}

function getReportData($reportId) {
    $filePath = __DIR__ . '/reports/' . $reportId . '.json';
    
    if (!file_exists($filePath)) {
        return null;
    }
    
    $reportData = json_decode(file_get_contents($filePath), true);
    
    // TO ENABLE EXPIRATION CHECK: Uncomment the lines below
    // Check if report has expired (if you want automatic expiration)
    // if (isset($reportData['expires_at']) && strtotime($reportData['expires_at']) < time()) {
    //     unlink($filePath); // Delete expired report
    //     return null;
    // }
    
    return $reportData;
}

function getReportIdByWebsiteUrl($websiteUrl) {
    // Find the most recent report for this website URL
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        return null;
    }
    
    $files = glob($reportsDir . '/*.json');
    $mostRecent = null;
    $mostRecentTime = 0;
    
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && isset($data['website_url']) && $data['website_url'] === $websiteUrl) {
            $fileTime = filemtime($file);
            if ($fileTime > $mostRecentTime) {
                $mostRecentTime = $fileTime;
                $mostRecent = basename($file, '.json');
            }
        }
    }
    
    return $mostRecent;
}

// =================================================================
// 🔧 MANUAL CLEANUP FUNCTIONS (Always available)
// =================================================================

// Manual cleanup function - can be called anytime regardless of settings
function manualCleanupOldReports($daysOld = 30) {
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        return "Reports directory not found.";
    }
    
    $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
    $files = glob($reportsDir . '/*.json');
    $deletedCount = 0;
    $totalFiles = count($files);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoffTime) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
    
    return "Manual cleanup complete: Deleted $deletedCount old reports out of $totalFiles total files.";
}

// Get storage statistics
function getStorageStats() {
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        return [
            'total_reports' => 0,
            'total_size_mb' => 0,
            'oldest_report' => null,
            'newest_report' => null
        ];
    }
    
    $files = glob($reportsDir . '/*.json');
    $totalSize = 0;
    $oldestTime = null;
    $newestTime = null;
    
    foreach ($files as $file) {
        $size = filesize($file);
        $time = filemtime($file);
        
        $totalSize += $size;
        
        if ($oldestTime === null || $time < $oldestTime) {
            $oldestTime = $time;
        }
        
        if ($newestTime === null || $time > $newestTime) {
            $newestTime = $time;
        }
    }
    
    return [
        'total_reports' => count($files),
        'total_size_mb' => round($totalSize / 1024 / 1024, 2),
        'oldest_report' => $oldestTime ? date('Y-m-d H:i:s', $oldestTime) : null,
        'newest_report' => $newestTime ? date('Y-m-d H:i:s', $newestTime) : null
    ];
}

// =================================================================
// 📊 REPORT COUNTING FUNCTIONS (For live counters and stats)
// =================================================================

// Get count of reports generated this week
function getReportsThisWeek() {
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        return 0;
    }
    
    // Calculate start of this week (Monday)
    $startOfWeek = strtotime('monday this week', time());
    
    $files = glob($reportsDir . '/*.json');
    $weeklyCount = 0;
    
    foreach ($files as $file) {
        $fileTime = filemtime($file);
        if ($fileTime >= $startOfWeek) {
            $weeklyCount++;
        }
    }
    
    return $weeklyCount;
}

// Get count of reports generated today
function getReportsToday() {
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        return 0;
    }
    
    // Calculate start of today
    $startOfToday = strtotime('today', time());
    
    $files = glob($reportsDir . '/*.json');
    $todayCount = 0;
    
    foreach ($files as $file) {
        $fileTime = filemtime($file);
        if ($fileTime >= $startOfToday) {
            $todayCount++;
        }
    }
    
    return $todayCount;
}

// =================================================================
// 📝 HOW TO ENABLE CLEANUP:
// =================================================================
// 
// 1. Uncomment the $cleanupSettings array at the top
// 2. Uncomment the define() statements 
// 3. Uncomment the cleanup call in storeReportData()
// 4. Adjust the settings:
//    - 'enabled' => true     (turn on/off)
//    - 'days' => 365         (keep for 1 year, or change to 30, 90, etc.)
//    - 'chance' => 10        (run 10% of the time, or change to 5, 20, etc.)
//    - 'log' => true         (log cleanup activity)
//
// The cleanup will then run automatically every time a new report is stored,
// with a X% chance (based on your 'chance' setting).
//
// =================================================================
?>
