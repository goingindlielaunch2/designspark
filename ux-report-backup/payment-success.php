<?php
// File: payment-success.php
// =================================================================
// This script handles successful payments using cURL (no SDK required)
// =================================================================

require_once 'stripe-config.php';

$session_id = $_GET['session_id'] ?? '';

if (empty($session_id)) {
    header('Location: index-WIP-7.php');
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
    
    // TODO: Here you would typically:
    // 1. Save the purchase to your database
    // 2. Generate or retrieve the advanced report
    // 3. Send confirmation email
    
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
                        Thank you <?php echo htmlspecialchars($customer_name); ?>! Your advanced website report is ready.
                    </p>
                    <div class="mt-6 space-y-4">
                        <a href="view-report.php?session=<?php echo urlencode($session_id); ?>" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View Your Report
                        </a>
                        <a href="index-WIP-7.html" 
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Return to Website
                        </a>
                    </div>
                    
                    <!-- Email confirmation note -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Email Confirmation</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>A copy of your report has been sent to <?php echo htmlspecialchars($customer_email); ?></p>
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
