<?php
// File: create-checkout-session.php
// =================================================================
// This script creates a Stripe Checkout session using cURL (no SDK required)
// =================================================================

require_once 'stripe-config.php';

header('Content-Type: application/json');

try {
    // Get the posted data
    $input = json_decode(file_get_contents('php://input'), true);
    
    $customer_email = $input['customer_email'] ?? '';
    $website_url = $input['website_url'] ?? '';
    $customer_name = $input['customer_name'] ?? '';
    $report_id = $input['report_id'] ?? '';
    
    // Validate required fields
    if (empty($customer_email) || empty($website_url) || empty($report_id)) {
        throw new Exception('Missing required fields');
    }
    
    // Prepare the data for Stripe API
    $postData = [
        'payment_method_types[]' => 'card',
        'line_items[0][price]' => STRIPE_PRICE_ID,
        'line_items[0][quantity]' => 1,
        'mode' => 'payment',
        'success_url' => SUCCESS_URL,
        'cancel_url' => CANCEL_URL,
        'customer_email' => $customer_email,
        'metadata[website_url]' => $website_url,
        'metadata[customer_name]' => $customer_name,
        'metadata[product_type]' => 'website_evaluation_report',
        'metadata[report_id]' => $report_id,
        'automatic_tax[enabled]' => 'false'
    ];
    
    // Convert array to URL-encoded string
    $postString = http_build_query($postData);
    
    // Initialize cURL
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.stripe.com/v1/checkout/sessions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postString,
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
    
    $responseData = json_decode($response, true);
    
    if ($httpCode !== 200) {
        $errorMessage = $responseData['error']['message'] ?? 'Unknown Stripe error';
        throw new Exception('Stripe error: ' . $errorMessage);
    }
    
    echo json_encode(['id' => $responseData['id']]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
