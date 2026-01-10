<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        <!-- Meta Tags -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="keywords" content="Chronus, Project, Management, Charity">
		<meta name="description" content="">
		<meta name='copyright' content='Chronus Solutions'>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Title -->
        <title>Chronus Solutions - Your Projects' Professionals</title>
		
		<!-- Favicon -->
		<link rel="icon" type="image/x-icon" href="favicon.ico">
		
		<!-- Google Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- Nice Select CSS -->
		<link rel="stylesheet" href="css/nice-select.css">
		<!-- Font Awesome CSS -->
        <link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- icofont CSS -->
        <link rel="stylesheet" href="css/icofont.css">
		<!-- Slicknav -->
		<link rel="stylesheet" href="css/slicknav.min.css">
		<!-- Owl Carousel CSS -->
        <link rel="stylesheet" href="css/owl-carousel.css">
		<!-- Datepicker CSS -->
		<link rel="stylesheet" href="css/datepicker.css">
		<!-- Animate CSS -->
        <link rel="stylesheet" href="css/animate.min.css">
		<!-- Magnific Popup CSS -->
        <link rel="stylesheet" href="css/magnific-popup.css">
		
		<!-- CSS -->
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="css/responsive.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<link rel="stylesheet" href="form.css">
        <script src="form.js"></script>
		
    </head>
    
    <body class="is-preload">
	
		<!-- Preloader -->
        <div class="preloader">
            <div class="loader">
                <div class="loader-outter"></div>
                <div class="loader-inner"></div>

                <div class="indicator"> 
                    <svg width="24px" height="24px">
                        <polyline id="back" points="12,2 8,8 2,12 8,16 12,22 15,16 22,12 15,7 12,2"></polyline>
                        <polyline id="front" points="12,2 8,8 2,12 8,16 12,22 15,16 22,12 15,7 12,2"></polyline>
                    </svg>
                </div>
            </div>
        </div>

			<?php include 'inc/header.php'; ?>


		<!-- Breadcrumbs -->
		<div class="breadcrumbs overlay">
			<div class="container">
				<div class="bread-inner">
					<div class="row">
						<div class="col-12">
							<h2>Events</h2>
							<ul class="bread-list">
								<li><a href="index.php">Home</a></li>
								<li><i class="icofont-simple-right"></i></li>
								<li class="active">Events</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
	
		<!-- Start Projects Details Area -->
		


<?php
$events = include __DIR__ . '/inc/events-data.php';
?>

<section class="events-list section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <?php for ($i = 0; $i < count($events); $i++): $e = $events[$i]; ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title"><a href="#"><?= htmlspecialchars($e['title']); ?></a></h3>
                            <p class="text-muted"><i class="fa fa-clock-o"></i> <?= htmlspecialchars($e['date']); ?> &nbsp; | &nbsp; <i class="fa fa-map-marker"></i> <?= htmlspecialchars($e['location']); ?></p>
                            <p><?= htmlspecialchars($e['summary']); ?></p>
                            <p class="small text-secondary"><?= htmlspecialchars($e['details']); ?></p>
                            <div class="mt-3">
                                <a href="events/register.php?event=<?= $i ?>" class="btn btn-primary">Register</a>
                                <a href="#" class="btn btn-outline-secondary">Learn More</a>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="col-lg-4 col-12">
                <div class="sidebar">
                    <h4>Upcoming Events</h4>
                    <ul class="list-unstyled">
                        <?php for ($j = 0; $j < min(5, count($events)); $j++): ?>
                            <li class="mb-2"><strong><?= htmlspecialchars($events[$j]['title']); ?></strong><br/><small class="text-muted"><?= htmlspecialchars($events[$j]['date']); ?> — <?= htmlspecialchars($events[$j]['location']); ?></small></li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

			<!-- End Events Area -->
    
</section>
		
		<!-- End Projects Details Area -->
		
<?php include 'ft.html'; ?>