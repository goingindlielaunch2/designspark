<?php
// File: report-storage.php
// =================================================================
// Simple file-based storage for AI reports (can be replaced with database later)
// =================================================================

function storeReportData($websiteUrl, $reportData) {
    // Create a unique report ID based on website URL and timestamp
    $reportId = md5($websiteUrl . time());
    
    // Ensure reports directory exists
    $reportsDir = __DIR__ . '/reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
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
    
    $data = file_get_contents($filePath);
    return json_decode($data, true);
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
?>
