<?php
// Stripe helper - returns keys from environment variables
// Set these in your system or .env file (not committed to git)

$secret = getenv('STRIPE_SECRET_KEY');
$publishable = getenv('STRIPE_PUBLISHABLE_KEY');

// For local development: add your test keys to Windows environment variables
// Or create a .env file and load it with vlucas/phpdotenv
if (!$secret || !$publishable) {
    throw new Exception('Stripe API keys not configured. Set STRIPE_SECRET_KEY and STRIPE_PUBLISHABLE_KEY environment variables.');
}

return ['secret' => $secret, 'publishable' => $publishable];
