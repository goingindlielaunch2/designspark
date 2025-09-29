<?php
// Test script for report storage functionality
require_once 'report-storage.php';

// Test data
$testUrl = 'https://example.com';
$testReportData = [
    'website_url' => $testUrl,
    'customer_name' => 'Test User',
    'customer_email' => 'test@example.com',
    'report_data' => [
        'overallScore' => 85,
        'basicReport' => [
            'scores' => [
                'HeroClarity' => 80,
                'VisualDesignLayout' => 90,
                'CallToAction' => 85,
                'ReadabilityTypography' => 88,
                'SocialProofTrust' => 75
            ],
            'topOpportunities' => [
                'Test opportunity 1',
                'Test opportunity 2'
            ]
        ],
        'advancedReport' => [
            'scores' => [
                'HeroClarity' => 80,
                'VisualDesignLayout' => 90,
                'CallToAction' => 85,
                'ReadabilityTypography' => 88,
                'SocialProofTrust' => 75,
                'HeroTrustSignals' => 70,
                'PersuasiveCopy' => 82,
                'AttentionRatio' => 78,
                'NavigationClarity' => 88,
                'AccessibilityContrast' => 92,
                'MobileResponsiveHints' => 95
            ],
            'advancedFeedback' => [
                'HeroClarity' => [
                    'feedback' => 'Test feedback for hero clarity',
                    'example' => 'Test example for hero clarity'
                ]
            ],
            'abTestIdeas' => [
                'Test A/B test idea 1',
                'Test A/B test idea 2'
            ]
        ]
    ],
    'created_at' => date('Y-m-d H:i:s')
];

echo "Testing report storage functions...\n\n";

// Test 1: Store report data
echo "1. Testing storeReportData()...\n";
$reportId = storeReportData($testUrl, $testReportData);
echo "   Report ID generated: " . $reportId . "\n";
echo "   Status: " . ($reportId ? "✅ SUCCESS" : "❌ FAILED") . "\n\n";

// Test 2: Retrieve report data by ID
echo "2. Testing getReportData()...\n";
$retrievedData = getReportData($reportId);
echo "   Retrieved data: " . ($retrievedData ? "✅ SUCCESS" : "❌ FAILED") . "\n";
if ($retrievedData) {
    echo "   Website URL: " . $retrievedData['website_url'] . "\n";
    echo "   Overall Score: " . $retrievedData['report_data']['overallScore'] . "\n";
}
echo "\n";

// Test 3: Find report by website URL
echo "3. Testing getReportIdByWebsiteUrl()...\n";
$foundReportId = getReportIdByWebsiteUrl($testUrl);
echo "   Found report ID: " . $foundReportId . "\n";
echo "   Status: " . ($foundReportId === $reportId ? "✅ SUCCESS" : "❌ FAILED") . "\n\n";

// Test 4: Clean up
echo "4. Cleaning up test data...\n";
$testFile = __DIR__ . '/reports/' . $reportId . '.json';
if (file_exists($testFile)) {
    unlink($testFile);
    echo "   Test file removed: ✅ SUCCESS\n";
} else {
    echo "   Test file not found: ❌ FAILED\n";
}

echo "\nAll tests completed!\n";
?>
