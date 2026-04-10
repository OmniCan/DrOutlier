<?php
/**
 * Temporary Simple Homepage - No Dependencies Required
 * Use this until composer install is run on server
 */

// Start session
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Outlier Radiology</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Lottie -->
    <script src="https://unpkg.com/@lottiefiles/dotlottie-web@latest/dist/dotlottie-web.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header-wrapper">
        <div class="header-inner">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <div class="col-lg-4 col-2">
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar">
                            <img src="/images/Toggle.svg" class="img-fluid" alt="Toggle" />
                        </button>
                    </div>
                    
                    <div class="col-lg-4 col-7 text-center">
                        <a class="navbar-brand col-md-2" href="/">
                            <img src="/images/Header-Logo.webp" class="img-fluid" alt="Dr Outlier Radiology" />
                        </a>
                    </div>

                    <div class="col-lg-4 col-2 d-flex justify-content-end">
                        <button class="btn btn-link loginBtn d-lg-block d-none">
                            <i class="fa-solid fa-user"></i> Login
                        </button>
                        <button class="btn btn-link searchBtn d-lg-none d-block">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>

                    <!-- Offcanvas Menu -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDarkNavbar">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title">
                                <span>Welcome to <strong>Dr. Outlier</strong></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="/">Home <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/theory-notes">Theory Notes <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/new-spotters">Spotters <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/new-osce">OSCE <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/new-exam-cases">AI-Rad <i class="fa-solid fa-chevron-right"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-wrapper">
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="content">
                            <h1 class="text-white">Welcome to Dr. Outlier Radiology</h1>
                            <p>
                                Struggling with MD, DNB, or DMRD exams? Dr. Outlier is built to make preparation simpler and more effective. 
                                It breaks down complex theory into clear, usable concepts and helps you perform confidently in practical exams. 
                                Think of it as a reliable study partner that helps you remember better, answer smarter, and stay calm during exams. 
                                With focused content and exam-oriented guidance, Dr. Outlier makes passing feel achievable rather than overwhelming.
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-lg-10 m-auto">
                                <div class="row">
                                    <!-- SPOTTERS -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/new-spotters">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-spotters" style="width: 174px; height: 182px;"></div>
                                                <h6 style="margin-top: 10px;">SPOTTERS</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- NOTES -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/theory-notes">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-notes" style="width: 174px; height: 182px;"></div>
                                                <h6>NOTES</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- OSCE -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/new-osce">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-osce" style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></div>
                                                <h6>OSCE</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- AI-RAD -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/new-exam-cases">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-airad" style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></div>
                                                <h6>AI-Rad</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- PRACTICAL ESSENTIALS -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/new-table-viva">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-practical" style="width: 174px; height: 182px;"></div>
                                                <h6>Practical <br> Essentials</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- WATCH & LEARN -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/watch-and-learn">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-watch" style="width: 174px; height: 182px;"></div>
                                                <h6>WATCH &amp; <br> LEARN</h6>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-logo">
                        <a href="/"><img src="/images/Footer-Logo.webp" class="img-fluid" alt="Dr Outlier" /></a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="social-media-wrapper">
                        <div class="social-media">
                            <ul>
                                <li><a target="_blank" href="https://api.whatsapp.com/send?phone=918554872707"><img src="/images/whatsapp-icon.webp" alt="WhatsApp" /></a></li>
                                <li><a href="https://www.youtube.com/@droutlier" target="_blank"><img src="/images/youtube-icon.svg" alt="YouTube" /></a></li>
                                <li><a href="https://www.instagram.com/dr.outlier/" target="_blank"><img src="/images/instagram-icon.webp" alt="Instagram" /></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-menu">
                        <ul>
                            <li><a href="/new-spotters">Spotters</a></li>
                            <li><a href="/theory-notes">Notes</a></li>
                            <li><a href="/new-osce">OSCE</a></li>
                            <li><a href="/new-exam-cases">AI-Rad</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="copyRight">
                <div class="row">
                    <div class="col-lg-6">
                        <ul>
                            <li><a href="tel:+918554872707"><i class="fa-solid fa-phone"></i> +91-8554872707</a></li>
                            <li><a href="mailto:droutlierradiology@gmail.com"><i class="fa-solid fa-envelope"></i> droutlierradiology@gmail.com</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-6"><p>© 2025&nbsp;Dr Outlier</p></div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>

    <!-- Lottie Animations -->
    <script>
    if (typeof DotLottie !== 'undefined') {
        new DotLottie({container: document.getElementById('lottie-spotters'), src: '/animantion/Blue circle 2.json', loop: true, autoplay: true});
        new DotLottie({container: document.getElementById('lottie-notes'), src: '/animantion/Green circle.json', loop: true, autoplay: true});
        new DotLottie({container: document.getElementById('lottie-osce'), src: '/animantion/Blue circle 2.json', loop: true, autoplay: true});
        new DotLottie({container: document.getElementById('lottie-airad'), src: '/animantion/green.json', loop: true, autoplay: true});
        new DotLottie({container: document.getElementById('lottie-practical'), src: '/animantion/green.json', loop: true, autoplay: true});
        new DotLottie({container: document.getElementById('lottie-watch'), src: '/animantion/Grey circle.json', loop: true, autoplay: true});
    }
    </script>
</body>
</html>
