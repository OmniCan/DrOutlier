<?php
/**
 * Temporary Simple Homepage - No Dependencies Required
 * Use this until composer install is run on server
 */

// Start session
session_start();

$pageTitle = 'Dr. Outlier Radiology';

// Include header
include __DIR__ . '/includes/header.php';
?>

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
                                        <a href="/spotters.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/Blue circle 2.json" loop autoplay style="width: 174px; height: 182px;"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0;">SPOTTERS</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- NOTES -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/theory-notes.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/Green circle.json" loop autoplay style="width: 174px; height: 182px;"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0;">NOTES</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- OSCE -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/osce.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/Blue circle 2.json" loop autoplay style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0;">OSCE</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- AI-RAD -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/ai-rad.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/green.json" loop autoplay style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0;">AI-Rad</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- PRACTICAL ESSENTIALS -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/practical-essentials.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/green.json" loop autoplay style="width: 174px; height: 182px;"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0; text-align: center;">Practical<br>Essentials</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- WATCH & LEARN -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/watch-learn.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/Grey circle.json" loop autoplay style="width: 174px; height: 182px;"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0; text-align: center;">WATCH &amp;<br>LEARN</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- QUIZORA -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/quizora.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; position: relative;">
                                                <dotlottie-player src="/public/animantion/Blue circle 2.json" loop autoplay style="width: 174px; height: 182px; filter: hue-rotate(223deg);"></dotlottie-player>
                                                <h6 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; margin: 0;">QUIZORA</h6>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="image">
                            <img src="/public/images/Dr-Outlier-Radiology.webp" class="img-fluid w-100" alt="Dr Outlier Radiology" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
// Additional JS for Lottie animations
$additionalJS = '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';

// Include footer
include __DIR__ . '/includes/footer.php';
?>
