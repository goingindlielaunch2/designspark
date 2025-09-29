<?php
// File: view-report.php
// =================================================================
// This script displays the advanced report after successful payment
// Uses cURL to verify payment with Stripe (no SDK required)
// =================================================================

require_once 'stripe-config.php';
require_once 'report-storage.php';
require_once 'review-functions.php';

$session_id = $_GET['session'] ?? '';

if (empty($session_id)) {
    header('Location: index.php');
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
    $report_id = $checkout_session['metadata']['report_id'] ?? '';
    
    // Retrieve the actual AI report data using the stored report ID
    $storedReportData = null;
    if ($report_id) {
        $storedReportData = getReportData($report_id);
    }
    
    if (!$storedReportData) {
        // Fall back to finding by website URL if report ID fails
        $report_id = getReportIdByWebsiteUrl($website_url);
        if ($report_id) {
            $storedReportData = getReportData($report_id);
        }
    }
    
    if (!$storedReportData || !isset($storedReportData['report_data'])) {
        throw new Exception('Could not retrieve report data for this purchase');
    }
    
    // Use the real AI report data
    $aiReportData = $storedReportData['report_data'];
    
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
    
    <!-- Review Section -->
    <?php 
    // Check if user has already reviewed this session
    $hasReviewed = hasUserReviewedSession($session_id);
    $customer_email = $checkout_session['customer_details']['email'] ?? '';
    ?>
    
    <?php if (!$hasReviewed): ?>
    <div class="mt-16 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-8 fade-in">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-blue-900 mb-3">🌟 How was your experience?</h3>
            <p class="text-blue-800 text-lg">Help other website owners by sharing your thoughts about our analysis service</p>
        </div>
        
        <form id="reviewForm" class="max-w-2xl mx-auto space-y-6">
            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
            <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($customer_email); ?>">
            <input type="hidden" name="website_url" value="<?php echo htmlspecialchars($website_url); ?>">
            <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
            <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id); ?>">
            
            <!-- Star Rating -->
            <div class="text-center">
                <label class="block text-lg font-semibold text-gray-700 mb-4">Rate your experience</label>
                <div class="flex justify-center space-x-2 mb-4" id="starRating">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                    <button type="button" class="star text-4xl text-gray-300 hover:text-yellow-400 transition-colors duration-200 transform hover:scale-110" data-rating="<?php echo $i; ?>">★</button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
                <p id="ratingText" class="text-gray-600 font-medium"></p>
            </div>
            
            <!-- Review Text -->
            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Share your thoughts (optional)</label>
                <textarea name="review_text" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="What did you think of our website analysis? How did it help your business?"></textarea>
                <p class="text-sm text-gray-500 mt-2">Your review will be published after approval to help other website owners.</p>
            </div>
            
            <div class="text-center">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    ✨ Submit Review
                </button>
            </div>
        </form>
        
        <div id="reviewSuccess" class="hidden mt-6 p-6 bg-green-50 border border-green-200 rounded-lg text-center">
            <div class="text-green-600 text-2xl mb-2">🎉</div>
            <h4 class="text-lg font-semibold text-green-800 mb-2">Thank you for your review!</h4>
            <p class="text-green-700">Your feedback has been submitted and will be published after approval. We appreciate you taking the time to help other website owners!</p>
        </div>
        
        <div id="reviewError" class="hidden mt-6 p-6 bg-red-50 border border-red-200 rounded-lg text-center">
            <div class="text-red-600 text-2xl mb-2">❌</div>
            <h4 class="text-lg font-semibold text-red-800 mb-2">Oops! Something went wrong</h4>
            <p class="text-red-700" id="errorMessage">Please try again or contact support if the problem persists.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="mt-16 bg-green-50 border border-green-200 rounded-xl p-8 text-center fade-in">
        <div class="text-green-600 text-3xl mb-3">✅</div>
        <h3 class="text-xl font-bold text-green-800 mb-2">Thank you for your review!</h3>
        <p class="text-green-700">You've already submitted a review for this report. We appreciate your feedback!</p>
    </div>
    <?php endif; ?>

    <script>
    // Star rating functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('ratingInput');
        const ratingText = document.getElementById('ratingText');
        let selectedRating = 0;
        
        const ratingTexts = {
            1: "😞 Poor - Not what I expected",
            2: "😐 Fair - Could be better", 
            3: "🙂 Good - Met my expectations",
            4: "😊 Very Good - Exceeded expectations",
            5: "🤩 Excellent - Amazing experience!"
        };
        
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                selectedRating = index + 1;
                ratingInput.value = selectedRating;
                updateStars();
                updateRatingText();
            });
            
            star.addEventListener('mouseenter', () => {
                highlightStars(index + 1);
                ratingText.textContent = ratingTexts[index + 1];
            });
        });
        
        document.getElementById('starRating').addEventListener('mouseleave', () => {
            updateStars();
            updateRatingText();
        });
        
        function updateStars() {
            stars.forEach((star, index) => {
                star.classList.toggle('text-yellow-400', index < selectedRating);
                star.classList.toggle('text-gray-300', index >= selectedRating);
            });
        }
        
        function highlightStars(rating) {
            stars.forEach((star, index) => {
                star.classList.toggle('text-yellow-400', index < rating);
                star.classList.toggle('text-gray-300', index >= rating);
            });
        }
        
        function updateRatingText() {
            if (selectedRating > 0) {
                ratingText.textContent = ratingTexts[selectedRating];
            } else {
                ratingText.textContent = '';
            }
        }
        
        // Handle form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedRating === 0) {
                alert('Please select a rating before submitting');
                return;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.textContent = '⏳ Submitting...';
            submitButton.disabled = true;
            
            const formData = new FormData(this);
            
            fetch('submit-review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.style.display = 'none';
                    document.getElementById('reviewSuccess').classList.remove('hidden');
                    
                    // Smooth scroll to success message
                    document.getElementById('reviewSuccess').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                } else {
                    document.getElementById('errorMessage').textContent = data.message || 'An error occurred';
                    document.getElementById('reviewError').classList.remove('hidden');
                    
                    // Reset button
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorMessage').textContent = 'Network error. Please check your connection and try again.';
                document.getElementById('reviewError').classList.remove('hidden');
                
                // Reset button
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        });
    });
    </script>
</body>
</html>
