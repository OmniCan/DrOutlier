<?php
/**
 * Quizora - Quiz Categories Page
 * Fetches and displays quiz categories from Laravel API
 */

session_start();

// Include helpers
require_once __DIR__ . '/includes/api-helper.php';

// Check authentication
requireAuth();

$pageTitle = 'Quizora - Dr. Outlier';
$user = getUser();
$token = getToken();

// Fetch quiz categories from API
$loading = true;
$categories = [];
$error = null;

// Using POST method with empty body and bearer token
$response = apiRequest('/quiz/categories', 'POST', [], $token);

if (isset($response['error'])) {
    $error = $response['error'];
    $loading = false;
} else {
    // Success - extract categories
    // Response structure: data.data (array of categories with quizzes)
    if (isset($response['data']['data'])) {
        $categories = $response['data']['data'];
    } elseif (isset($response['data'])) {
        $categories = $response['data'];
    }
    $loading = false;
}

// Array of random animation files for variety (blue/purple theme for quizora)
$animations = [
    '/public/animantion/Blue circle 2.json',
    '/public/animantion/Blue circle.json',
    '/public/animantion/Grey circle.json',
    '/public/animantion/green.json'
];

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- Loader -->
<?php if ($loading): ?>
<div class="loader-wrapper">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Page Header - Desktop -->
    <section class="Macaroni-Sign-page pt-0 d-none d-lg-block">
        <div class="container-fluid px-0">
            <div class="macaroni-top">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="content">
                                <h2 class="text-white mb-0">Quizora Categories</h2>
                            </div>
                        </div>
                        <div class="col-lg-6 text-end">
                            <h6 class="text-white mb-0">Select Quiz Category</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Grid -->
    <div class="container">
        <?php if ($error): ?>
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            </div>
        <?php elseif (!$loading && empty($categories)): ?>
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> No quiz categories available yet.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row justify-content-center mt-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <?php foreach ($categories as $index => $category): ?>
                            <?php 
                                $categoryId = $category['id'] ?? '';
                                $categoryName = $category['name'] ?? 'Untitled';
                                $categoryColor = $category['color'] ?? '';
                                $quizCount = isset($category['quizzes']) ? count($category['quizzes']) : 0;
                                
                                // Pick animation file based on index (for variety)
                                // Use blue/purple themed animations for quizora
                                $animationFile = $animations[$index % count($animations)];
                            ?>
                            <div class="col-md-4 col-sm-6 col-6" style="padding: 18px; flex: 0 0 20%; max-width: 20%;">
                                <a href="/quizora-chapters.php?id=<?php echo urlencode($categoryId); ?>" style="text-decoration: none;">
                                    <div class="box" style="display: flex; align-items: center; justify-content: center; text-align: center; border-radius: 15px; transition: all 0.3s ease; cursor: pointer; position: relative; width: 100%; aspect-ratio: 1 / 1;">
                                        <dotlottie-player 
                                            src="<?php echo htmlspecialchars($animationFile); ?>" 
                                            loop 
                                            autoplay 
                                            style="width: 100%; height: 100%; <?php echo $categoryColor ? 'filter: hue-rotate(' . $categoryColor . ');' : ''; ?>">
                                        </dotlottie-player>
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 75%;">
                                            <h6 style="color: white; font-size: 16px; font-weight: 600; margin: 0; word-wrap: break-word; line-height: 1.3;">
                                                <?php echo htmlspecialchars($categoryName); ?>
                                            </h6>
                                            <?php if ($quizCount > 0): ?>
                                            <small style="color: rgba(255, 255, 255, 0.8); font-size: 12px; display: block; margin-top: 5px;">
                                                <?php echo $quizCount; ?> Quiz<?php echo $quizCount > 1 ? 'zes' : ''; ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Additional JS for Lottie player
$additionalJS = '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';
// Include footer
include __DIR__ . '/includes/footer.php';
?>
