<?php
// Stripe webhook for checkout.session.completed
require_once __DIR__ . '/../vendor/autoload.php';
$keys = include __DIR__ . '/../inc/stripe.php';
$secret = getenv('STRIPE_WEBHOOK_SECRET') ?: null;
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!$secret) {
    http_response_code(400);
    echo 'Webhook secret not configured.';
    exit;
}

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit;
}

// handle the event
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $metadata = $session->metadata ?? null;
    $registrationId = $metadata->registration_id ?? null;

    // update DB and mark as paid
    $pdo = include __DIR__ . '/../inc/db.php';
    include __DIR__ . '/../inc/mailer.php';

    if ($registrationId) {
        update_registration($pdo, (int)$registrationId, ['status' => 'paid', 'stripe_session_id' => $session->id]);
        // fetch registration to send email
        $reg = find_registration_by_id($pdo, (int)$registrationId);
        if ($reg) {
            send_registration_email($reg['email'], $reg['name'], $reg['event_title'], 'paid', $reg['notes'] ?? '');
        }
    } else {
        // fallback: try to find by session id or metadata
        // Not implemented here
    }
}

http_response_code(200);
