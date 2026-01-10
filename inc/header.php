<?php
// Shared header include
$base = (basename(dirname($_SERVER['PHP_SELF'])) === 'contact') ? '../' : '';
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Header layout CSS moved to style.css to apply site-wide -->

<!-- Header Area -->
<header class="header">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-5 col-12">
                    <ul class="top-link">
                        <li><a href="https://www.facebook.com/profile.php?id=61553116751154" target="_blank"><i class="icofont-facebook"></i></a></li>
                        <li><a href="https://x.com/ChronusSol?t=Odip_3eYnZaapua06bP4JA&s=09" target="_blank"><i class="icofont-twitter"></i></a></li>
                        <li><a href="https://www.instagram.com/chronussolutions?igsh=MWlnbW1jbnliMXZldw==" target="_blank"><i class="icofont-instagram"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/chronussolutions/posts/?feedView=all" target="_blank"><i class="icofont-linkedin"></i></a></li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-7 col-12">
                    <ul class="top-contact">
                        <li><i class="fa fa-envelope"></i><a href="mailto:info@chronussolutions.co.uk">info@chronussolutions.co.uk</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->

    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="inner">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-12">
                        <div class="logo">
                            <a href="<?= $base ?>index.php"><img src="<?= $base ?>img/chronus-logo.jpg" alt="#"></a>
                        </div>
                        <div class="mobile-nav"></div>
                    </div>
                    <div class="col-lg-7 col-md-9 col-12">
                        <!-- Main Menu -->
                        <div class="main-menu">
                            <nav class="navigation">
                                <ul class="nav menu">
                                    <li class="<?= ($currentFile == 'index.php' && $currentDir != 'contact') ? 'active' : '' ?>"><a href="<?= $base ?>index.php">Home</a></li>
                                    <li class="<?= ($currentFile == 'about.php') ? 'active' : '' ?>"><a href="<?= $base ?>about.php">About Us</a></li>
                                    <li class="<?= ($currentFile == 'services.php') ? 'active' : '' ?>"><a href="<?= $base ?>services.php">Our Services </a></li>
                                    <li class="<?= ($currentFile == 'projects.php') ? 'active' : '' ?>"><a href="<?= $base ?>projects.php">Projects</a></li>
                                    <li class="<?= ($currentFile == 'cv-tools.php') ? 'active' : '' ?>"><a href="<?= $base ?>cv-tools.php">Career Tools</a></li>
                                    <li class="<?= ($currentFile == 'events.php') ? 'active' : '' ?>"><a href="<?= $base ?>events.php">Events</a></li>
                                    <li class="<?= ($currentDir == 'contact') ? 'active' : '' ?>"><a href="<?= $base ?>contact/">Contact Us</a></li>
                                </ul>
                            </nav>
                        </div>
                        <!--/ End Main Menu -->
                    </div>
                    <div class="col-lg-2 col-12">
                        <div class="get-quote">
                            <a href="<?= $base ?>career-assessment.php" class="btn">Career Assessment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>
<!-- End Header Area -->

<script>
// Remove duplicate headers/topbars if old markup remains on a page (helps when include and old header both exist)
document.addEventListener('DOMContentLoaded', function() {
    try {
        const headers = document.querySelectorAll('header.header');
        if (headers.length > 1) {
            for (let i = 1; i < headers.length; i++) {
                headers[i].parentNode.removeChild(headers[i]);
            }
        }
        const topbars = document.querySelectorAll('.topbar');
        if (topbars.length > 1) {
            for (let i = 1; i < topbars.length; i++) {
                topbars[i].parentNode.removeChild(topbars[i]);
            }
        }
    } catch (e) {
        // fail silently
        console.warn('Header de-duplication script error:', e);
    }
});
</script>
