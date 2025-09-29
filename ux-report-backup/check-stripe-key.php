<?php
// Quick test to verify the Stripe key is loaded correctly
require_once 'stripe-config.php';
echo "Stripe Publishable Key: " . STRIPE_PUBLISHABLE_KEY;
echo "<br>";
echo "Key Type: " . (strpos(STRIPE_PUBLISHABLE_KEY, 'pk_live_') === 0 ? 'LIVE MODE' : (strpos(STRIPE_PUBLISHABLE_KEY, 'pk_test_') === 0 ? 'TEST MODE' : 'UNKNOWN'));
?>
