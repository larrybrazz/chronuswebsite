<?php
// Stripe helper - returns keys
// Set STRIPE_SECRET_KEY and STRIPE_PUBLISHABLE_KEY in environment variables or define them in a non-committed file.
$secret = getenv('STRIPE_SECRET_KEY') ?: (defined('STRIPE_SECRET_KEY') ? STRIPE_SECRET_KEY : null);
$publishable = getenv('STRIPE_PUBLISHABLE_KEY') ?: (defined('STRIPE_PUBLISHABLE_KEY') ? STRIPE_PUBLISHABLE_KEY : null);
return ['secret' => $secret, 'publishable' => $publishable];
