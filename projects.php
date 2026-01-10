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
							<h2>Projects at Chronus</h2>
							<ul class="bread-list">
								<li><a href="index.html">Home</a></li>
								<li><i class="icofont-simple-right"></i></li>
								<li class="active">Projects</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
	
		<!-- Start Projects Details Area -->
		
		<?php
            $projects = [
            [
            "title" => "Chronus Website Project 1.0",
            "status" => "completed",
            "date" => "03 March 2024",
            "overview" => "Designed a modern, user-friendly website to enhance Chronus Solutions’ online presence and improve accessibility for clients.",
            "achievements" => [
                "Cost savings simplified the platform, cutting maintenance and hosting costs by 30%.",
                "Improved accessibility made the website mobile-friendly and easier to navigate, helping clients find what they need 25% faster.",
                "Better service added self-service tools and live chat, speeding up response times by 50% and boosting satisfaction.",
                "Stronger online presence enhanced SEO and analytics, driving 35% more traffic and providing insights to improve services."
            ],
            ],
            [
            "title" => "Project Uche",
            "status" => "completed",
            "date" => "05 May 2024",
            "overview" => "Developed a scalable, multivendor e-commerce platform, enabling seamless interactions between vendors and customers in a user-friendly interface.",
            "achievements" => [
                "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
                "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
                "Built a flexible system to support future growth and handle increased traffic seamlessly.",
                "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],
            [
            "title" => "Project Uddy",
            "status" => "completed",
            "date" => "01 June 2024",
            "overview" => "Created a personalized website for a music artist, providing fans with an easy way to connect and stream music.",
            "achievements" => [
                "Centralizing fan engagement and music streaming on a single platform reduced reliance on third-party services, cutting recurring costs such as hosting, streaming, and transaction processing fees. ",
                "The tailored platform offered seamless navigation, direct access to exclusive content, and real-time updates, fostering deeper engagement and loyalty among fans.",
                "By integrating multiple functions—such as content distribution, event announcements, and fan interactions—into one platform, the artist’s operational efficiency improved significantly.",
                "The platform reduced the need for external advertising and distribution platforms, lowering promotional overhead while maintaining full control of the artist's branding and communication. ", 
                "The website enabled direct-to-fan sales for music, merchandise, and tickets, bypassing intermediaries and ensuring higher margins for the artist. ",
                "A visually appealing and highly functional website enhanced the artist's digital footprint, reinforcing their professional image and providing a cohesive space for their online identity. "
            ]
            ],
            [
            "title" => "Project E-Sekure",
            "status" => "in-progress",
            "progress"=> 70,
            "date" => "06 July 2024",
            "overview" => "Developing a secure, modern platform aimed at providing innovative safety solutions for individuals and organizations.",
            "achievements" => [
            //     "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
            //     "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
            //     "Built a flexible system to support future growth and handle increased traffic seamlessly.",
            //     "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],
            [
            "title" => "Chronus Website Project 2.0",
            "progress"=> 60,
            "status" => "in-progress",
            "date" => "03 October 2024",
            "overview" => "A complete revamp of Chronus 1.0, introducing enhanced features, improved design, and a more intuitive user experience.",
            "achievements" => [
            //     "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
            //     "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
            //     "Built a flexible system to support future growth and handle increased traffic seamlessly.",
            //     "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],
            [
            "title" => "Project AnkaraAfrica",
            "status" => "in-progress",
            "progress"=> 80,
            "date" => "03 November 2024",
            "overview" => "Creating a robust multivendor e-commerce platform to connect African sellers and buyers in a dynamic marketplace.",
            "achievements" => [
            //     "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
            //     "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
            //     "Built a flexible system to support future growth and handle increased traffic seamlessly.",
            //     "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],
            [
            "title" => "Project Sure Care",
            "status" => "in-progress",
            "progress"=> 30,
            "date" => "03 December 2024",
            "overview" => "Consultancy project aimed at improving operational efficiency and service delivery for a domiciliary care service provider.",
            "achievements" => [
            //     "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
            //     "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
            //     "Built a flexible system to support future growth and handle increased traffic seamlessly.",
            //     "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],

            [
            "title" => "Project Green Planet",
            "status" => "in-progress",
            "progress"=> 50,
            "date" => "13 April 2024",
            "overview" => "Developing an initiative to promote sustainability and environmental awareness through innovative solutions and community engagement.",
            "achievements" => [
            //     "Improved efficiency, streamlined vendor-customer interactions, reduced operational overhead, and saved costs.",
            //     "Enhanced user experience, delivering a user-friendly platform that boosted customer satisfaction and vendor engagement.",
            //     "Built a flexible system to support future growth and handle increased traffic seamlessly.",
            //     "Integrated tools for real-time updates and simplified transactions, improving service speed and reliability.", 
            ]
            ],
            ]
        ?>

<section class="news-single section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <?php foreach ($projects as $project): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="single-main">
                                <!-- Project Title -->
                                <h1 class="news-title">
                                    <a href="#"><?= $project['title']; ?></a>
                                </h1>

                                <!-- Meta Information -->
                                <div class="meta">
                                    <div class="meta-left">
                                        <span class="date">
                                            <i class="fa fa-clock-o"></i> Commenced: <?= $project['date']; ?>
                                        </span>
                                    </div>
                                    <div class="meta-right" style="margin-top: 10px;">
                                        <h6>Status:</h6>
                                        <?php if ($project['status'] == 'completed'): ?>
                                            <i class="fa fa-check-circle fa-2x" style="color: green;"></i> Completed
                                        <?php else: ?>
                                            <div class="progress" style="padding: 20px 30px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                    role="progressbar" 
                                                    style="width: <?= $project['progress']; ?>%; color: black;" 
                                                    aria-valuenow="<?= $project['progress']; ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    <?= $project['progress']; ?>% Complete
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Project Overview -->
                                <div class="news-text">
                                    <h6>Project Overview:</h6>
                                    <p><?= $project['overview']; ?></p>
                                </div>

                                <!-- Key Achievements -->
                                <?php if (!empty($project['achievements'])): ?>
                                    <h6>Key Achievements:</h6>
                                    <ol class="key-achievements">
                                        <?php foreach ($project['achievements'] as $achievement): ?>
                                            <li><?php echo $achievement; ?></li>
                                        <?php endforeach; ?>
                                    </ol>

                                    <!-- Expand/Collapse Button -->
                                    <input class="expand-label-btn" type="checkbox" id="expand-<?= $project['title']; ?>">
                                <?php endif; ?>


                                <!-- Conditional Join Button -->
                                <?php if ($project['status'] != 'completed'): ?>
                                    <a href="contact/" class="btn" style="color: white" >Join the Project</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br/>
                <?php endforeach; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="main-sidebar">
                    <!-- Categories Widget -->
                    <div class="single-widget category">
                        <h3 class="title">Project Categories</h3>
                        <ul class="categor-list">
                            <li><a href="#">Non-profit and Charities</a></li>
                            <li><a href="#">Environmental Sustainability</a></li>
                            <li><a href="#">Website Development</a></li>
                            <li><a href="#">Training and Education</a></li>
                            <li><a href="#">Sales and Marketing</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
		
		<!-- End Projects Details Area -->
		
	<?php include 'ft.html'; ?>