<?php
// File: test-stripe-connection.php
// =================================================================
// Test script to verify Stripe connection works without SDK
// =================================================================

require_once 'stripe-config.php';

echo "<h1>Stripe Connection Test</h1>";

// Test 1: Check if cURL is available
echo "<h2>1. cURL Support</h2>";
if (function_exists('curl_init')) {
    echo "✅ cURL is available<br>";
} else {
    echo "❌ cURL is NOT available - your host does not support cURL<br>";
    exit;
}

// Test 2: Check config values
echo "<h2>2. Configuration Check</h2>";
if (defined('STRIPE_SECRET_KEY') && strlen(STRIPE_SECRET_KEY) > 10) {
    echo "✅ Stripe Secret Key is configured<br>";
} else {
    echo "❌ Stripe Secret Key not configured properly<br>";
}

if (defined('STRIPE_PUBLISHABLE_KEY') && strlen(STRIPE_PUBLISHABLE_KEY) > 10) {
    echo "✅ Stripe Publishable Key is configured<br>";
} else {
    echo "❌ Stripe Publishable Key not configured properly<br>";
}

if (defined('STRIPE_PRICE_ID') && strlen(STRIPE_PRICE_ID) > 10) {
    echo "✅ Stripe Price ID is configured<br>";
} else {
    echo "❌ Stripe Price ID not configured properly<br>";
}

// Test 3: Test Stripe API connection
echo "<h2>3. Stripe API Connection Test</h2>";

try {
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.stripe.com/v1/prices/' . STRIPE_PRICE_ID,
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
    
    if ($httpCode === 200) {
        $priceData = json_decode($response, true);
        echo "✅ Successfully connected to Stripe API<br>";
        echo "Price amount: $" . ($priceData['unit_amount'] / 100) . " " . strtoupper($priceData['currency']) . "<br>";
    } else {
        $errorData = json_decode($response, true);
        echo "❌ Stripe API error (HTTP $httpCode): " . ($errorData['error']['message'] ?? 'Unknown error') . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
}

echo "<br><p><strong>If all tests pass, your Stripe integration should work correctly!</strong></p>";
echo "<p><a href='index-WIP-7.php'>← Back to main page</a></p>";
?>
