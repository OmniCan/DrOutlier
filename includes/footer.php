<?php
/**
 * Footer Include - Reusable Footer
 */
?>
    <!-- Footer -->
    <footer class="footer-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-logo">
                        <a href="/"><img src="/public/images/logo.png" alt="Dr Outlier" style="max-height: 60px; width: auto; object-fit: contain;" /></a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="social-media-wrapper">
                        <div class="social-media">
                            <ul>
                                <li><a target="_blank" href="https://api.whatsapp.com/send?phone=918554872707"><img src="/public/images/whatsapp-icon.webp" alt="WhatsApp" /></a></li>
                                <li><a href="https://www.youtube.com/@droutlier" target="_blank"><img src="/public/images/youtube-icon.svg" alt="YouTube" /></a></li>
                                <li><a href="https://www.instagram.com/dr.outlier/" target="_blank"><img src="/public/images/instagram-icon.webp" alt="Instagram" /></a></li>
                            </ul>
                        </div>
                        <div class="play-app" style="display: flex; gap: 5px;">
                            <div class="google-pay">
                                <button class="btn link-btn appstore" onclick="alert('Use your browser menu to Add to Home Screen.');">
                                    <div style="display: flex;"><img src="/public/images/aapstore.svg" alt="App Store" /></div>
                                </button>
                            </div>
                            <div class="google-pay">
                                <a href="/public/DrOutlier.apk" download="DrOutlier.apk" class="btn link-btn" style="text-decoration: none; padding: 0px;">
                                    <div style="display: flex;"><img src="/public/images/playstore.svg" alt="Play Store" /></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="footer-menu">
                        <ul>
                            <li><a href="/spotters.php">Spotters</a></li>
                            <li><a href="/theory-notes.php">Notes</a></li>
                            <li><a href="/osce.php">OSCE</a></li>
                            <li><a href="/ai-rad.php">AI-Rad</a></li>
                            <li><a href="/practical-essentials.php">Practical Essentials</a></li>
                            <li><a href="/watch-learn.php">Watch &amp; Learn</a></li>
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
    
    <?php if (isset($additionalJS)) echo $additionalJS; ?>
</body>
</html>

<?php
// Include login modals if user is not authenticated
if (!isset($_SESSION['user_token'])) {
    include __DIR__ . '/login-modals.php';
}
?>
