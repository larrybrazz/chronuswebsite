<?php
require_once __DIR__ . '/../vendor/autoload.php';
$keys = include __DIR__ . '/../inc/stripe.php';
\Stripe\Stripe::setApiKey($keys['secret'] ?? '');

$sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : null;
$free = isset($_GET['free']);
$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
$events = include __DIR__ . '/../inc/events-data.php';
$eventTitle = $events[$eventId]['title'] ?? null;

$registration = [];
$pdo = include __DIR__ . '/../inc/db.php';
if ($free) {
    // free flow passes reg_id
    $regId = isset($_GET['reg_id']) ? (int)$_GET['reg_id'] : null;
    if ($regId) {
        $reg = find_registration_by_id($pdo, $regId);
        if ($reg) {
            $registration = $reg;
        }
    } else {
        $registration['status'] = 'free-registered';
    }
} elseif ($sessionId) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        if ($session && $session->payment_status === 'paid') {
            $registration['status'] = 'paid';
            $registration['name'] = $session->metadata->name ?? '';
            $registration['email'] = $session->metadata->email ?? '';
            $registration['event_title'] = $session->metadata->event_title ?? '';
            // try to read DB record
            if (!empty($session->metadata->registration_id)) {
                $reg = find_registration_by_id($pdo, (int)$session->metadata->registration_id);
                if ($reg) { $registration = $reg; }
            }
        } else {
            $registration['status'] = 'unknown';
        }
    } catch (Exception $e) {
        $registration['status'] = 'error';
        $registration['error'] = $e->getMessage();
    }
}

// compute site base for links
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$parts = explode('/', trim($script, '/'));
$site_base = '/';
if (count($parts) > 1) { $site_base = '/' . $parts[0] . '/'; }
include __DIR__ . '/../inc/header.php';
?>
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h2>Registration complete</h2>
                <?php if ($registration['status'] === 'paid' || $registration['status'] === 'free-registered'): ?>
                    <p>Thanks <?= htmlspecialchars($registration['name']) ?> — your registration for <strong><?= htmlspecialchars($registration['event_title'] ?? $eventTitle) ?></strong> is confirmed.</p>
                    <p>We've sent a confirmation to <strong><?= htmlspecialchars($registration['email']) ?></strong> (if an email was provided).</p>
                    <?php if (!empty($registration['notes'])): ?>
                        <p><strong>Your notes:</strong> <?= nl2br(htmlspecialchars($registration['notes'])) ?></p>
                    <?php endif; ?>
                <?php elseif ($registration['status'] === 'unknown'): ?>
                    <p>Your payment has not been confirmed yet. If you were charged, please contact us with your receipt and we'll follow up.</p>
                <?php else: ?>
                    <p>There was a problem completing your registration: <?= htmlspecialchars($registration['error'] ?? 'Unknown error') ?></p>
                <?php endif; ?>
                <a href="<?= htmlspecialchars((isset($site_base) ? $site_base : '/')) ?>events.php" class="btn btn-secondary">Back to events</a>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../ft.html'; ?>