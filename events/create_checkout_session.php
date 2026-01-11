<?php
// DEBUG: Show PHP errors to diagnose HTTP 500 locally
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Read and validate POST
$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
$eventTitle = isset($_POST['event_title']) ? $_POST['event_title'] : 'Event';
$amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0; // stored in smallest currency unit (pence)
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
$terms = isset($_POST['terms']) ? (int) $_POST['terms'] : 0;

// Basic validation
if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Content-Type: application/json');
    ob_clean();
    http_response_code(400);
    die(json_encode(['error' => 'Name and valid email are required.']));
}

if ($terms !== 1) {
    header('Content-Type: application/json');
    ob_clean();
    http_response_code(400);
    die(json_encode(['error' => 'You must agree to the terms.']));
}

// DB and mail helpers
$pdo = include __DIR__ . '/../inc/db.php';
include __DIR__ . '/../inc/mailer.php';

// Insert registration as pending (paid events) or registered (free)
$regStatus = ($amount > 0) ? 'pending' : 'registered';
$regId = insert_registration($pdo, [
    'event_id' => $eventId,
    'event_title' => $eventTitle,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'amount' => $amount,
    'notes' => $notes,
    'status' => $regStatus,
]);

// Build success/cancel urls
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
// Allow subfolder deployments (e.g., /chronuswebsite-main)
$appBasePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
$baseUrl = $protocol . '://' . $host . ($appBasePath ? $appBasePath : '');

try {
    if ($amount > 0) {
        // For paid events, load and initialize Stripe
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            throw new Exception('Stripe SDK not installed. Run: composer require stripe/stripe-php');
        }
        require_once __DIR__ . '/../vendor/autoload.php';
        $keys = include __DIR__ . '/../inc/stripe.php';
        if (empty($keys['secret'])) {
            throw new Exception('STRIPE_SECRET_KEY not configured in environment');
        }
        if (!class_exists('Stripe\\Stripe')) {
            throw new Exception('Stripe PHP library missing. Run: composer require stripe/stripe-php');
        }
        \Stripe\Stripe::setApiKey($keys['secret']);
        
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => ['name' => $eventTitle],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'event_id' => $eventId,
                'event_title' => $eventTitle,
                'registration_id' => $regId,
                'notes' => mb_substr($notes, 0, 500),
            ],
            'success_url' => $baseUrl . '/events/success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/events/register.php?event=' . $eventId,
        ]);

        // Update registration with session id
        update_registration($pdo, $regId, ['stripe_session_id' => $session->id]);

        // For form POST without JS, issue a redirect to Stripe-hosted checkout
        ob_clean();
        if (!$session->url) {
            http_response_code(500);
            die(json_encode(['error' => 'Stripe session created but no checkout URL returned.']));
        }
        error_log('Stripe checkout URL: ' . $session->url);
        header('Location: ' . $session->url);
        exit;
    } else {
        // Free event - registration already created with 'registered' status
        // send confirmation email (includes notes)
        send_registration_email($email, $name, $eventTitle, 'registered', $notes);
        // Redirect directly to success page
        ob_clean();
        header('Location: ' . $baseUrl . '/events/success.php?free=1&reg_id=' . $regId);
        exit;
    }
} catch (Throwable $e) {
    // Rollback / mark registration as error
    update_registration($pdo, $regId, ['status' => 'error']);
    $errorMsg = $e->getMessage();
    error_log('Stripe checkout error: ' . $errorMsg);
    ob_clean();
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    die('<html><body style="font-family: sans-serif; margin: 40px;"><h2>Payment Error</h2><p>' . htmlspecialchars($errorMsg) . '</p><p><a href="javascript:history.back()">Go Back</a></p></body></html>');
}
