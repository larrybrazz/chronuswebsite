<?php
// Events registration page - render form for selected event
$events = include __DIR__ . '/../inc/events-data.php';
$idx = isset($_GET['event']) ? (int) $_GET['event'] : null;
if ($idx === null || !isset($events[$idx])) {
    include __DIR__ . '/../hd.html';
    ?>
    <div class="container my-5">
        <div class="alert alert-warning">No event selected. <a href="../events.php">Back to events</a></div>
    </div>
    <?php
    include __DIR__ . '/../ft.html';
    exit;
}
$event = $events[$idx];
// Prefill from querystring if present (e.g., redirected from contact with ?name=&email=)
$prefill_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
$prefill_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$prefill_phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';
$prefill_notes = isset($_GET['notes']) ? htmlspecialchars($_GET['notes']) : '';
include __DIR__ . '/../hd.html';
?>

<!-- Breadcrumbs -->
<?php
// compute site base so links resolve correctly from subfolders
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$parts = explode('/', trim($script, '/'));
$site_base = '/';
if (count($parts) > 1) { $site_base = '/' . $parts[0] . '/'; }
?>
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Register — <?= htmlspecialchars($event['title']); ?></h2>
                    <ul class="bread-list">
                        <li><a href="<?= htmlspecialchars($site_base) ?>index.php">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li><a href="<?= htmlspecialchars($site_base) ?>events.php">Events</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Register</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- End Breadcrumbs -->
		



<section class="section provovis">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($event['title']); ?></h3>
                        <p class="text-muted"><i class="fa fa-clock-o"></i> <?= htmlspecialchars($event['date']); ?> &nbsp; | &nbsp; <i class="fa fa-map-marker"></i> <?= htmlspecialchars($event['location']); ?></p>
                        <p class="lead"><?= htmlspecialchars($event['summary']); ?></p>
                        <hr>
                        <p><?= nl2br(htmlspecialchars($event['details'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card registration-card">
                    <div class="card-body">
                        <h5 class="mb-3">Registration</h5>
                        <p class="mb-2">Price: <strong><?= ($event['price'] > 0) ? '£' . number_format($event['price'] / 100, 2) : '<span class="text-success">Free</span>'; ?></strong></p>
                        <form id="regForm" action="<?= htmlspecialchars($site_base) ?>events/create_checkout_session.php" method="post" novalidate>
                            <input type="hidden" name="event_id" value="<?= $idx ?>">
                            <input type="hidden" name="event_title" value="<?= htmlspecialchars($event['title']); ?>">
                            <input type="hidden" name="amount" value="<?= (int)$event['price']; ?>">

                            <div class="form-group mb-2">
                                <label for="name">Full name</label>
                                <input id="name" name="name" type="text" class="form-control" required aria-required="true" value="<?= $prefill_name ?>">
                            </div>

                            <div class="form-group mb-2">
                                <label for="email">Email address</label>
                                <input id="email" name="email" type="email" class="form-control" required aria-required="true" value="<?= $prefill_email ?>">
                            </div>

                            <div class="form-group mb-2">
                                <label for="phone">Phone</label>
                                <input id="phone" name="phone" type="tel" class="form-control" value="<?= $prefill_phone ?>">
                            </div>

                            <div class="form-group mb-2">
                                <label for="notes">Notes (optional)</label>
                                <textarea id="notes" name="notes" class="form-control" rows="2"><?= $prefill_notes ?></textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="terms" value="1" id="terms" required>
                                <label class="form-check-label small" for="terms">I agree to the terms and allow Chronus to contact me regarding this event.</label>
                            </div>

                            <button id="submitBtn" type="submit" class="btn btn-primary btn-block w-100"><?= ($event['price'] > 0) ? 'Pay & Register' : 'Register (Free)'; ?></button>

                            <div class="mt-3 text-center">
                                <a href="<?= htmlspecialchars($site_base) ?>events.php" class="btn btn-link">← Back to events</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



				<!-- End Events Area -->

		<!-- End Projects Details Area -->
		
	<?php include __DIR__ . '/../ft.html'; ?>