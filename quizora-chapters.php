<?php
/**
 * Quizora - Quiz List Page
 * Shows quizzes within a selected category
 */

session_start();

// Authentication check
require_once __DIR__ . '/includes/api-helper.php';
requireAuth();

$pageTitle = 'Quizora - Quizzes';

// Get category ID from URL
$categoryId = $_GET['id'] ?? null;

if (!$categoryId) {
    die('Category ID required');
}

$token = getToken();
$userId = $_SESSION['user_id'] ?? '';

// Fetch quiz categories and filter by category ID
$apiResponse = apiRequest('/quiz/categories', 'POST', [], $token);

$quizzes = [];
$categoryName = 'Quizzes';

if (isset($apiResponse['data']['data'])) {
    $categories = $apiResponse['data']['data'];
    
    // Find the selected category
    foreach ($categories as $category) {
        if (($category['id'] ?? '') == $categoryId) {
            $categoryName = $category['name'] ?? 'Quizzes';
            $quizzes = $category['quizzes'] ?? [];
            break;
        }
    }
} elseif (isset($apiResponse['data'])) {
    $categories = $apiResponse['data'];
    foreach ($categories as $category) {
        if (($category['id'] ?? '') == $categoryId) {
            $categoryName = $category['name'] ?? 'Quizzes';
            $quizzes = $category['quizzes'] ?? [];
            break;
        }
    }
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<style>
.quiz-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px 30px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.quiz-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(10px);
    box-shadow: 0 8px 25px rgba(18, 110, 151, 0.3);
    border-color: #126E97;
}

.quiz-status-badge {
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-unattempted {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
}

.status-paused {
    background: #FFA500;
    color: white;
}

.status-completed {
    background: #4CAF50;
    color: white;
}
</style>

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
                                <h2 class="text-white mb-0"><?php echo htmlspecialchars($categoryName); ?></h2>
                            </div>
                        </div>
                        <div class="col-lg-6 text-end">
                            <h6 class="text-white mb-0">Select Quiz</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quizzes List -->
        <div class="container">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="list-wrapper" style="display: flex; flex-direction: column; gap: 15px;">
                        <?php if (!empty($quizzes)): ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <?php
                                    $quizId = $quiz['id'] ?? '';
                                    $quizName = $quiz['quiz_name'] ?? $quiz['name'] ?? 'Unnamed Quiz';
                                    $quizStatus = $quiz['quiz_status'] ?? 0; // 0=unattempted, 1=completed, 2=paused
                                    $totalQuestions = $quiz['total_questions'] ?? 0;
                                    $completedQuestions = $quiz['completed_questions'] ?? 0;
                                    $isBookmarked = $quiz['is_bookmarked'] ?? false;
                                    
                                    // Determine status badge
                                    $statusClass = 'status-unattempted';
                                    $statusText = 'Not Started';
                                    if ($quizStatus == 1) {
                                        $statusClass = 'status-completed';
                                        $statusText = 'Completed';
                                    } elseif ($quizStatus == 2) {
                                        $statusClass = 'status-paused';
                                        $statusText = 'In Progress';
                                    }
                                    
                                    // Link would be to quiz start/resume page
                                    // For now, using a generic link - you can customize this
                                    $link = "#"; // Replace with actual quiz page when available
                                ?>
                                <a href="<?php echo htmlspecialchars($link); ?>" style="text-decoration: none;">
                                    <div class="quiz-card">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 60px; height: 60px; flex-shrink: 0; margin-right: 20px;">
                                                <dotlottie-player 
                                                    src="/public/animantion/Blue circle.json" 
                                                    loop 
                                                    autoplay 
                                                    style="width: 100%; height: 100%;">
                                                </dotlottie-player>
                                            </div>
                                            <div style="flex: 1;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <h6 style="color: white; margin: 0; font-size: 18px; font-weight: 600;">
                                                        <?php echo htmlspecialchars($quizName); ?>
                                                    </h6>
                                                    <?php if ($isBookmarked): ?>
                                                        <i class="fas fa-bookmark" style="color: #FFA500; font-size: 14px;"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="quiz-status-badge <?php echo $statusClass; ?>">
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                    <?php if ($totalQuestions > 0): ?>
                                                        <small style="color: rgba(255, 255, 255, 0.7);">
                                                            <i class="fas fa-question-circle me-1"></i>
                                                            <?php echo $totalQuestions; ?> questions
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if ($quizStatus == 2 && $completedQuestions > 0): ?>
                                                        <small style="color: rgba(255, 255, 255, 0.7);">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            <?php echo $completedQuestions; ?>/<?php echo $totalQuestions; ?> completed
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div style="margin-left: 20px;">
                                                <i class="fa-solid fa-chevron-right" style="color: rgba(255, 255, 255, 0.5); font-size: 20px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 60px 0;">
                                <i class="fas fa-question-circle" style="font-size: 64px; color: rgba(255, 255, 255, 0.2); margin-bottom: 20px;"></i>
                                <h4 style="color: rgba(255, 255, 255, 0.7);">No quizzes available</h4>
                                <p style="color: rgba(255, 255, 255, 0.5);">Check back later for new quizzes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile View Header -->
    <section class="Macaroni-Sign-page pt-0 d-block d-lg-none">
        <div class="container-fluid px-0">
            <div class="macaroni-top">
                <div class="container">
                    <div class="content text-center">
                        <h2 class="text-white mb-2"><?php echo htmlspecialchars($categoryName); ?></h2>
                        <h6 class="text-white mb-0">Select Quiz</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Quizzes List -->
        <div class="container px-3">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="list-wrapper" style="display: flex; flex-direction: column; gap: 12px;">
                        <?php if (!empty($quizzes)): ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <?php
                                    $quizId = $quiz['id'] ?? '';
                                    $quizName = $quiz['quiz_name'] ?? $quiz['name'] ?? 'Unnamed Quiz';
                                    $quizStatus = $quiz['quiz_status'] ?? 0;
                                    $totalQuestions = $quiz['total_questions'] ?? 0;
                                    $completedQuestions = $quiz['completed_questions'] ?? 0;
                                    $isBookmarked = $quiz['is_bookmarked'] ?? false;
                                    
                                    $statusClass = 'status-unattempted';
                                    $statusText = 'Not Started';
                                    if ($quizStatus == 1) {
                                        $statusClass = 'status-completed';
                                        $statusText = 'Completed';
                                    } elseif ($quizStatus == 2) {
                                        $statusClass = 'status-paused';
                                        $statusText = 'In Progress';
                                    }
                                    
                                    $link = "#"; // Replace with actual quiz page
                                ?>
                                <a href="<?php echo htmlspecialchars($link); ?>" style="text-decoration: none;">
                                    <div class="quiz-card" style="padding: 15px 20px;">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 45px; height: 45px; flex-shrink: 0; margin-right: 15px;">
                                                <dotlottie-player 
                                                    src="/public/animantion/Blue circle.json" 
                                                    loop 
                                                    autoplay 
                                                    style="width: 100%; height: 100%;">
                                                </dotlottie-player>
                                            </div>
                                            <div style="flex: 1;">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 style="color: white; margin: 0; font-size: 15px; font-weight: 600;">
                                                        <?php echo htmlspecialchars($quizName); ?>
                                                    </h6>
                                                    <?php if ($isBookmarked): ?>
                                                        <i class="fas fa-bookmark" style="color: #FFA500; font-size: 12px;"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="quiz-status-badge <?php echo $statusClass; ?>" style="font-size: 11px; padding: 3px 8px;">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </div>
                                            <div style="margin-left: 15px;">
                                                <i class="fa-solid fa-chevron-right" style="color: rgba(255, 255, 255, 0.5); font-size: 16px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px 0;">
                                <i class="fas fa-question-circle" style="font-size: 48px; color: rgba(255, 255, 255, 0.2); margin-bottom: 15px;"></i>
                                <h5 style="color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">No quizzes available</h5>
                                <p style="color: rgba(255, 255, 255, 0.5); font-size: 14px;">Check back later for new quizzes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// Additional JS for Lottie player
$additionalJS = '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';
// Include footer
include __DIR__ . '/includes/footer.php';
?>
