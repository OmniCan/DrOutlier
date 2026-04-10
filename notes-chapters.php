<?php
/**
 * Theory Notes - Chapters List Page
 * Shows chapters within a selected category
 */

session_start();

// Authentication check
require_once __DIR__ . '/includes/api-helper.php';
requireAuth();

$pageTitle = 'Theory Notes - Chapters';

// Get category ID from URL
$categoryId = $_GET['id'] ?? null;
$parentId = $_GET['parentId'] ?? null;

if (!$categoryId) {
    die('Category ID required');
}

// Fetch chapters from API
$apiResponse = apiRequest('/api/theory-notes/chapters', 'POST', [
    'category_id' => $categoryId
], getToken());

$chapters = $apiResponse['data']['chapters'] ?? [];
$categoryName = $apiResponse['data']['category_name'] ?? 'Chapters';

// Include header
include __DIR__ . '/includes/header.php';
?>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Page Header -->
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
                            <h6 class="text-white mb-0">Select Chapter</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chapters List -->
        <div class="container">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="list-wrapper" style="display: flex; flex-direction: column; gap: 15px;">
                        <?php if (!empty($chapters)): ?>
                            <?php foreach ($chapters as $index => $chapter): ?>
                                <?php
                                    $chapterId = $chapter['id'] ?? '';
                                    $chapterName = $chapter['name'] ?? 'Unnamed Chapter';
                                    $firstItemId = $chapter['first_item_id'] ?? null;
                                    $chapterColor = $chapter['color'] ?? '';
                                    
                                    // Determine link based on whether chapter has direct items
                                    if ($firstItemId) {
                                        $link = "/notes-view.php?id={$chapterId}&itemId={$firstItemId}&parentId={$categoryId}#page1";
                                    } else {
                                        $link = "/notes-chapters.php?id={$chapterId}&parentId={$categoryId}";
                                    }
                                ?>
                                <a href="<?php echo htmlspecialchars($link); ?>" style="text-decoration: none;">
                                    <div class="list-item" style="
                                        display: flex;
                                        align-items: center;
                                        padding: 20px 30px;
                                        background: rgba(255, 255, 255, 0.05);
                                        backdrop-filter: blur(10px);
                                        border: 1px solid rgba(255, 255, 255, 0.1);
                                        border-radius: 15px;
                                        transition: all 0.3s ease;
                                        cursor: pointer;
                                        position: relative;
                                        overflow: hidden;
                                    " onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(10px)'; this.style.boxShadow='0 8px 25px rgba(30, 79, 253, 0.3)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.transform='translateX(0)'; this.style.boxShadow='none';">
                                        <div style="
                                            position: relative;
                                            width: 60px;
                                            height: 60px;
                                            flex-shrink: 0;
                                            margin-right: 20px;
                                        ">
                                            <dotlottie-player 
                                                src="/public/animantion/Blue circle 2.json" 
                                                loop 
                                                autoplay 
                                                style="width: 100%; height: 100%; <?php echo $chapterColor ? "filter: hue-rotate({$chapterColor});" : ''; ?>">
                                            </dotlottie-player>
                                        </div>
                                        <div style="flex: 1;">
                                            <h6 style="color: white; margin: 0; font-size: 18px; font-weight: 600;">
                                                <?php echo htmlspecialchars($chapterName); ?>
                                            </h6>
                                        </div>
                                        <div style="margin-left: 20px;">
                                            <i class="fa-solid fa-chevron-right" style="color: rgba(255, 255, 255, 0.5); font-size: 20px;"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 60px 0;">
                                <i class="fas fa-folder-open" style="font-size: 64px; color: rgba(255, 255, 255, 0.2); margin-bottom: 20px;"></i>
                                <h4 style="color: rgba(255, 255, 255, 0.7);">No chapters available</h4>
                                <p style="color: rgba(255, 255, 255, 0.5);">Check back later for updates</p>
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
                        <h6 class="text-white mb-0">Select Chapter</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Chapters List -->
        <div class="container px-3">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="list-wrapper" style="display: flex; flex-direction: column; gap: 12px;">
                        <?php if (!empty($chapters)): ?>
                            <?php foreach ($chapters as $index => $chapter): ?>
                                <?php
                                    $chapterId = $chapter['id'] ?? '';
                                    $chapterName = $chapter['name'] ?? 'Unnamed Chapter';
                                    $firstItemId = $chapter['first_item_id'] ?? null;
                                    $chapterColor = $chapter['color'] ?? '';
                                    
                                    if ($firstItemId) {
                                        $link = "/notes-view.php?id={$chapterId}&itemId={$firstItemId}&parentId={$categoryId}#page1";
                                    } else {
                                        $link = "/notes-chapters.php?id={$chapterId}&parentId={$categoryId}";
                                    }
                                ?>
                                <a href="<?php echo htmlspecialchars($link); ?>" style="text-decoration: none;">
                                    <div class="list-item" style="
                                        display: flex;
                                        align-items: center;
                                        padding: 15px 20px;
                                        background: rgba(255, 255, 255, 0.05);
                                        backdrop-filter: blur(10px);
                                        border: 1px solid rgba(255, 255, 255, 0.1);
                                        border-radius: 12px;
                                        transition: all 0.3s ease;
                                    ">
                                        <div style="width: 45px; height: 45px; flex-shrink: 0; margin-right: 15px;">
                                            <dotlottie-player 
                                                src="/public/animantion/Blue circle 2.json" 
                                                loop 
                                                autoplay 
                                                style="width: 100%; height: 100%; <?php echo $chapterColor ? "filter: hue-rotate({$chapterColor});" : ''; ?>">
                                            </dotlottie-player>
                                        </div>
                                        <div style="flex: 1;">
                                            <h6 style="color: white; margin: 0; font-size: 16px; font-weight: 600;">
                                                <?php echo htmlspecialchars($chapterName); ?>
                                            </h6>
                                        </div>
                                        <div style="margin-left: 15px;">
                                            <i class="fa-solid fa-chevron-right" style="color: rgba(255, 255, 255, 0.5); font-size: 18px;"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px 0;">
                                <i class="fas fa-folder-open" style="font-size: 48px; color: rgba(255, 255, 255, 0.2); margin-bottom: 15px;"></i>
                                <h5 style="color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">No chapters available</h5>
                                <p style="color: rgba(255, 255, 255, 0.5); font-size: 14px;">Check back later for updates</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// Load Lottie player
$additionalJS = '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';

// Include footer
include __DIR__ . '/includes/footer.php';
?>
