<?php
/**
 * Temporary Simple Homepage - No Dependencies Required
 * Use this until composer install is run on server
 */

// Start session
session_start();

$pageTitle = 'Dr. Outlier Radiology';

// Additional head scripts for Lottie
$additionalCSS = '<script src="https://unpkg.com/@lottiefiles/dotlottie-web@latest/dist/dotlottie-web.js"></script>';

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
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-spotters" style="width: 174px; height: 182px;"></div>
                                                <h6 style="margin-top: 10px;">SPOTTERS</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- NOTES -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/theory-notes.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-notes" style="width: 174px; height: 182px;"></div>
                                                <h6>NOTES</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- OSCE -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/osce.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-osce" style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></div>
                                                <h6>OSCE</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- AI-RAD -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/ai-rad.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-airad" style="width: 174px; height: 182px; filter: hue-rotate(180deg);"></div>
                                                <h6>AI-Rad</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- PRACTICAL ESSENTIALS -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/practical-essentials.php">
                                            <div class="box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
                                                <div id="lottie-practical" style="width: 174px; height: 182px;"></div>
                                                <h6>Practical <br> Essentials</h6>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- WATCH & LEARN -->
                                    <div class="col-lg-4 col-6">
                                        <a href="/watch-learn.php">
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

<?php
// Additional JS for Lottie animations
$additionalJS = '
    <!-- Lottie Animations -->
    <script>
    if (typeof DotLottie !== "undefined") {
        new DotLottie({container: document.getElementById("lottie-spotters"), src: "/public/animantion/Blue circle 2.json", loop: true, autoplay: true});
        new DotLottie({container: document.getElementById("lottie-notes"), src: "/public/animantion/Green circle.json", loop: true, autoplay: true});
        new DotLottie({container: document.getElementById("lottie-osce"), src: "/public/animantion/Blue circle 2.json", loop: true, autoplay: true});
        new DotLottie({container: document.getElementById("lottie-airad"), src: "/public/animantion/green.json", loop: true, autoplay: true});
        new DotLottie({container: document.getElementById("lottie-practical"), src: "/public/animantion/green.json", loop: true, autoplay: true});
        new DotLottie({container: document.getElementById("lottie-watch"), src: "/public/animantion/Grey circle.json", loop: true, autoplay: true});
    }
    </script>
';

// Include footer
include __DIR__ . '/includes/footer.php';
?>
