<?php
// File: payment-success.php
// =================================================================
// This script handles successful payments using cURL (no SDK required)
// =================================================================

require_once 'stripe-config.php';

$session_id = $_GET['session_id'] ?? '';

if (empty($session_id)) {
    header('Location: index.php');
    exit;
}

try {
    // Retrieve the checkout session using cURL
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
        throw new Exception('Failed to retrieve payment session');
    }
    
    // Verify payment was successful
    if ($checkout_session['payment_status'] !== 'paid') {
        throw new Exception('Payment not completed');
    }
    
    // Get customer data from metadata and customer details
    $website_url = $checkout_session['metadata']['website_url'] ?? '';
    $customer_name = $checkout_session['metadata']['customer_name'] ?? '';
    $customer_email = $checkout_session['customer_details']['email'] ?? '';
    
    // Send confirmation email with report link
    $emailSent = false;
    if ($customer_email && $customer_name) {
        $reportUrl = "https://" . $_SERVER['HTTP_HOST'] . "/view-report.php?session=" . urlencode($session_id);
        
        $subject = "Your Website Analysis Report is Ready - DesignSpark";
        
        $emailBody = "
Hi {$customer_name},

Thank you for your purchase! Your detailed website analysis report is now ready.

🎯 Your Report Details:
• Website Analyzed: {$website_url}
• Report ID: {$session_id}

📊 View Your Report:
{$reportUrl}

Your report includes:
✓ Comprehensive UX/CRO analysis
✓ All 11 detailed metrics with scores
✓ Specific improvement recommendations
✓ Actionable examples for each issue
✓ A/B testing suggestions
✓ Downloadable PDF version

Need help implementing the recommendations? Reply to this email - we'd love to help you improve your website's performance!

Best regards,
The DesignSpark Team
hello@withdesignspark.com

---
This email was sent because you purchased a website analysis report from DesignSpark.
";

        // Set email headers
        $headers = [
            'From: DesignSpark <hello@withdesignspark.com>',
            'Reply-To: hello@withdesignspark.com',
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];
        
        // Send the email
        $emailSent = mail($customer_email, $subject, $emailBody, implode("\r\n", $headers));
        
        // Log email attempts for debugging
        if ($emailSent) {
            error_log("DesignSpark: Report email sent successfully to {$customer_email} for session {$session_id}");
        } else {
            error_log("DesignSpark: Failed to send report email to {$customer_email} for session {$session_id}");
        }
    }
    
    // TODO: Here you would typically:
    // 1. Save the purchase to your database ✓ (Done via Stripe)
    // 2. Generate or retrieve the advanced report ✓ (Done via report storage)
    // 3. Send confirmation email ✓ (Done above)
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Advanced Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <?php if (isset($error_message)): ?>
                <!-- Error State -->
                <div class="text-center fade-in">
                    <div class="mx-auto h-12 w-12 text-red-500">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <circle cx="24" cy="24" r="22" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18l12 12M30 18l-12 12"/>
                        </svg>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Payment Error</h2>
                    <p class="mt-2 text-sm text-gray-600"><?php echo htmlspecialchars($error_message); ?></p>
                    <div class="mt-6">
                        <a href="index-WIP-7.html" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Return to Website
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Success State -->
                <div class="text-center fade-in">
                    <div class="mx-auto h-12 w-12 text-green-500">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <circle cx="24" cy="24" r="22" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 24l4 4 8-8"/>
                        </svg>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Payment Successful!</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Thank you <?php echo htmlspecialchars($customer_name); ?>! Your advanced website report is ready to view.
                    </p>
                    <div class="mt-6 space-y-4">
                        <a href="view-report.php?session=<?php echo urlencode($session_id); ?>" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Your Report
                        </a>
                        <a href="index.php" 
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Return to Website
                        </a>
                    </div>
                    
                    <!-- Important notes -->
                    <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Your Report is Ready</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>Save the report link above - your detailed analysis is available to view and download immediately.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
