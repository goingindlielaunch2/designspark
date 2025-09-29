<?php
// File: view-report.php
// =================================================================
// This script displays the advanced report after successful payment
// Uses cURL to verify payment with Stripe (no SDK required)
// =================================================================

require_once 'stripe-config.php';

$session_id = $_GET['session'] ?? '';

if (empty($session_id)) {
    header('Location: index-WIP-7.php');
    exit;
}

try {
    // Retrieve the checkout session using cURL to verify payment
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.stripe.com/v1/checkout/sessions/' . $session_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . STRIPE_SECRET_KEY,
            'Content-Type: application/x-www-form-urlencoded'
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_errno($curl)) {
        throw new Exception('cURL error: ' . curl_error($curl));
    }
    
    curl_close($curl);
    
    $checkout_session = json_decode($response, true);
    
    if ($httpCode !== 200) {
        $errorMessage = $checkout_session['error']['message'] ?? 'Unknown Stripe error';
        throw new Exception('Stripe error: ' . $errorMessage);
    }
    
    if ($checkout_session['payment_status'] !== 'paid') {
        throw new Exception('Payment not verified');
    }
    
    $website_url = $checkout_session['metadata']['website_url'] ?? '';
    $customer_name = $checkout_session['metadata']['customer_name'] ?? '';
    
    // TODO: Here you would retrieve the actual AI report data
    // For now, we'll simulate the data structure
    $aiReportData = [
        'overallScore' => 73,
        'advancedReport' => [
            'scores' => [
                'HeroClarity' => 68,
                'VisualDesignLayout' => 82,
                'CallToAction' => 65,
                'ReadabilityTypography' => 90,
                'SocialProofTrust' => 60
            ],
            'advancedFeedback' => [
                'HeroClarity' => [
                    'feedback' => 'Your hero section needs clearer messaging about what you do and how you help customers.',
                    'example' => 'Instead of "We provide solutions", try "We increase your website conversions by 40% in 30 days".'
                ],
                'CallToAction' => [
                    'feedback' => 'Your call-to-action buttons could be more prominent and action-oriented.',
                    'example' => 'Change "Learn More" to "Get My Free Quote" and make it larger with contrasting colors.'
                ]
            ],
            'abTestIdeas' => [
                'Test different hero headlines focusing on specific benefits',
                'Try larger, more colorful CTA buttons',
                'Add customer testimonials above the fold'
            ]
        ]
    ];
    
} catch (Exception $e) {
    header('Location: payment-success.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Advanced Website Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="no-print bg-white rounded-lg shadow-sm p-6 mb-8 fade-in">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Your Advanced Website Report</h1>
                    <p class="text-gray-600 mt-2">Analysis for: <?php echo htmlspecialchars($website_url); ?></p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Print Report
                    </button>
                    <button onclick="downloadPDF()" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div id="report-content" class="bg-white rounded-lg shadow-sm p-8 fade-in">
            <div class="mb-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        ✅ Payment Verified - Full Access Granted
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Overall Score: <?php echo $aiReportData['overallScore']; ?>/100</h2>
                </div>
            </div>

            <!-- Detailed Feedback Sections -->
            <div class="space-y-8">
                <?php foreach ($aiReportData['advancedReport']['advancedFeedback'] as $category => $feedback): ?>
                    <?php 
                        $score = $aiReportData['advancedReport']['scores'][$category] ?? 0;
                        $categoryName = preg_replace('/([A-Z])/', ' $1', $category);
                        $categoryName = trim($categoryName);
                        
                        if ($score >= 85) {
                            $scoreClass = 'bg-green-100 text-green-800';
                        } elseif ($score >= 50) {
                            $scoreClass = 'bg-yellow-100 text-yellow-800';
                        } else {
                            $scoreClass = 'bg-red-100 text-red-800';
                        }
                    ?>
                    <div class="border border-gray-200 rounded-xl p-6">
                        <div class="flex gap-6 items-start">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full <?php echo $scoreClass; ?> flex items-center justify-center font-bold text-2xl">
                                    <?php echo $score; ?>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($categoryName); ?></h3>
                                <div class="text-gray-700 leading-relaxed space-y-3">
                                    <p><?php echo htmlspecialchars($feedback['feedback']); ?></p>
                                    <div class="p-3 bg-gray-50 border-l-4 border-gray-200 italic">
                                        <span class="font-semibold not-italic">Example:</span> 
                                        <?php echo htmlspecialchars($feedback['example']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- A/B Test Ideas -->
            <?php if (!empty($aiReportData['advancedReport']['abTestIdeas'])): ?>
                <div class="mt-12 no-print">
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-xl font-bold mb-4">Bonus: A/B Test Ideas for Experts</h3>
                        <div class="space-y-3">
                            <?php foreach ($aiReportData['advancedReport']['abTestIdeas'] as $idea): ?>
                                <div class="bg-white p-3 rounded-lg flex items-start gap-3">
                                    <span class="text-indigo-500 font-bold">Test:</span>
                                    <p class="text-gray-700"><?php echo htmlspecialchars($idea); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                <p class="text-gray-600 mb-4">
                    Report generated for: <strong><?php echo htmlspecialchars($customer_name); ?></strong>
                </p>
                <p class="text-sm text-indigo-600 font-semibold">
                    Need help implementing these changes? 
                    <a href="index-WIP-7.html#contact" class="underline">Contact us for a free consultation</a>
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="no-print mt-8 text-center">
            <a href="index-WIP-7.html" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                ← Return to Website
            </a>
        </div>
    </div>

    <script>
        function downloadPDF() {
            // Simple PDF generation using jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            const websiteUrl = "<?php echo addslashes($website_url); ?>";
            const customerName = "<?php echo addslashes($customer_name); ?>";
            const overallScore = <?php echo $aiReportData['overallScore']; ?>;
            
            let yPos = 20;
            const leftMargin = 15;
            const lineSpacing = 7;
            
            // Header
            doc.setFontSize(11);
            doc.setTextColor(54, 78, 163);
            doc.text("Analyzed Site: " + websiteUrl, leftMargin, yPos);
            yPos += lineSpacing * 1.5;
            doc.setTextColor(0, 0, 0);
            
            doc.setFontSize(22);
            doc.setFont("helvetica", "bold");
            doc.text("Advanced Website Report", leftMargin, yPos);
            yPos += lineSpacing * 2;
            
            doc.setFontSize(16);
            doc.text("Overall Score: " + overallScore + "/100", leftMargin, yPos);
            yPos += lineSpacing * 3;
            
            // Add feedback sections
            const feedback = <?php echo json_encode($aiReportData['advancedReport']['advancedFeedback']); ?>;
            const scores = <?php echo json_encode($aiReportData['advancedReport']['scores']); ?>;
            
            Object.keys(feedback).forEach(category => {
                if (yPos > 260) {
                    doc.addPage();
                    yPos = 20;
                }
                
                const categoryName = category.replace(/([A-Z])/g, ' $1').trim();
                const score = scores[category] || 0;
                
                doc.setFontSize(14);
                doc.setFont("helvetica", "bold");
                doc.text(categoryName + " (Score: " + score + ")", leftMargin, yPos);
                yPos += lineSpacing;
                
                doc.setFontSize(11);
                doc.setFont("helvetica", "normal");
                
                const feedbackLines = doc.splitTextToSize(feedback[category].feedback, 180);
                doc.text(feedbackLines, leftMargin, yPos);
                yPos += feedbackLines.length * lineSpacing;
                
                const exampleLines = doc.splitTextToSize("Example: " + feedback[category].example, 175);
                doc.setFont("helvetica", "italic");
                doc.text(exampleLines, leftMargin + 5, yPos);
                yPos += exampleLines.length * lineSpacing + lineSpacing * 1.5;
            });
            
            doc.save("Website-Report-" + customerName.replace(/\s+/g, '-') + ".pdf");
        }
    </script>
</body>
</html>
