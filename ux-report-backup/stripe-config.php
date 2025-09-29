<?php
// File: stripe-config.php
// =================================================================
// Stripe configuration file - Currently in TEST MODE for safe testing
// =================================================================

// *** CURRENTLY IN TEST MODE - SAFE FOR TESTING ***
// Test Keys (GET THESE FROM YOUR STRIPE DASHBOARD > DEVELOPERS > API KEYS)
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51QLvcZFTB7fZkt2EIIeLsLUrDAzg3HeGwu9UcMT4eSDJrFGqjJvwh3g7aLt5164wPoyrUcnjxrmOHphZ9Edp6lLi00gbw2WqD2'); // Get from Stripe Dashboard
define('STRIPE_SECRET_KEY', 'sk_test_51QLvcZFTB7fZkt2EjAM0haI073N2COLrlpjnY0gflyuzvnL44xAgEPmPi3z2p4qFXT5VEJhjh5UJPNyplqGNCEqZ00ZFzjCLhy'); // Get from Stripe Dashboard  

define('STRIPE_PRICE_ID', 'price_1RjX7FFTB7fZkt2Ehb13Tn1O'); // Create a test product/price in Stripe

// Live Keys (COMMENTED OUT FOR TESTING - uncomment when ready for production)
// define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51QLvcZFTB7fZkt2EsJCLa3B62zm0CAI7bhaMmF399YZMgUagoJCJsvLdyt6hiUG9FPtAezkF415RaRehrDUqwAZI00n8jKwfD0');
// define('STRIPE_SECRET_KEY', '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');
// define('STRIPE_PRICE_ID', 'price_1RiXQQFTB7fZkt2EnO1MqYDO');

// Website URLs (update these for production)
define('SUCCESS_URL', 'http://withdesignspark.com/payment-success.php?session_id={CHECKOUT_SESSION_ID}');
define('CANCEL_URL', 'http://withdesignspark.com/index-WIP-7.php');
?>
