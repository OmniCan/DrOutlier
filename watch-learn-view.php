<?php
/**
 * Watch & Learn - Video Viewer Page
 * Displays YouTube videos with navigation and bookmarks
 */

session_start();
require_once __DIR__ . '/includes/api-helper.php';
requireAuth();

$pageTitle = 'Watch & Learn - Dr. Outlier';
$user = getUser();
$token = getToken();
$userId = $_SESSION['user']['id'] ?? '';

$chapterId = $_GET['id'] ?? '';
$itemId = $_GET['itemId'] ?? '';
$parentId = $_GET['parentId'] ?? '';

$items = [];
$categoryName = '';
$error = null;

if ($chapterId) {
    $response = apiRequest('/watch-and-learn-category/items-by-chapter', 'POST', ['chapter_id' => $chapterId], $token);
    
    if (isset($response['error'])) {
        $error = $response['error'];
    } else {
        $items = $response['data']['items'] ?? [];
        if (!empty($items)) {
            $categoryName = $items[0]['categories']['name'] ?? $items[0]['chapter']['name'] ?? '';
        }
    }
}

$currentIndex = 0;
if ($itemId && !empty($items)) {
    foreach ($items as $index => $item) {
        if ($item['id'] == $itemId) {
            $currentIndex = $index;
            break;
        }
    }
}

$currentItem = $items[$currentIndex] ?? null;

// Extract YouTube video ID from link
function getYouTubeId($url) {
    if (empty($url)) return null;
    
    // Handle various YouTube URL formats
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        return $id[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
        return $id[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        return $id[1];
    } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
        return $id[1];
    }
    
    return null;
}

$videoId = null;
if ($currentItem && isset($currentItem['youtube_link'])) {
    $videoId = getYouTubeId($currentItem['youtube_link']);
}

$additionalCSS = <<<CSS
<style>
    body { background: #1B1E27; }
    .video-viewer-container { background: #1B1A1A; border-radius: 24px; overflow: hidden; margin: 30px auto; max-width: 1400px; }
    .video-wrapper {
        background: #2A2A2A;
        padding: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 600px;
        position: relative;
    }
    .video-responsive {
        position: relative;
        width: 100%;
        max-width: 1200px;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
    }
    .video-responsive iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 12px;
    }
    .bookmark-btn {
        padding: 8px 12px;
        background: white;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.60);
        cursor: pointer;
        transition: all 0.3s;
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }
    .bookmark-btn.active svg path {
        fill: url(#bookmarkGradient);
    }
    .item-nav {
        background: #1B1A1A;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }
    .item-nav-btn {
        padding: 8px 38px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s;
    }
    .item-nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .item-counter {
        background: rgba(0, 0, 0, 0.60);
        border-radius: 12px;
        border: 1px solid #1B1A1A;
        padding: 7px 15px;
        color: white;
        font-size: 16px;
    }
    .category-title-bar {
        width: 100%;
        height: 62px;
        background: #282D41;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0;
    }
    .category-title-bar h2 {
        color: white;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
    }
    .video-info {
        background: #1B1E27;
        padding: 30px 40px;
        color: white;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .video-title {
        font-size: 24px;
        font-weight: 600;
        color: #44A6C5;
        margin-bottom: 15px;
    }
    .video-description {
        font-size: 15px;
        line-height: 1.8;
        color: rgba(255,255,255,0.9);
    }
</style>
CSS;

include __DIR__ . '/includes/header.php';
?>

<div class="category-title-bar">
    <h2><?php echo htmlspecialchars($categoryName); ?></h2>
</div>

<div class="main-wrapper" style="background: #1B1E27; min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($currentItem): ?>
            <div class="video-viewer-container">
                <!-- Bookmark Button -->
                <button class="bookmark-btn" id="bookmarkBtn" onclick="toggleBookmark()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" fill="#1E1E1E"/>
                        <defs>
                            <linearGradient id="bookmarkGradient" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#44A6C5"/>
                                <stop offset="100%" stop-color="#1E4FFD"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </button>

                <!-- Video Area -->
                <div class="video-wrapper">
                    <?php if ($videoId): ?>
                        <div class="video-responsive">
                            <iframe 
                                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    <?php else: ?>
                        <p style="color: white; text-align: center;">No video available</p>
                    <?php endif; ?>
                </div>

                <!-- Video Info -->
                <?php if (!empty($currentItem['title']) || !empty($currentItem['description'])): ?>
                <div class="video-info">
                    <?php if (!empty($currentItem['title'])): ?>
                        <h3 class="video-title"><?php echo htmlspecialchars($currentItem['title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($currentItem['description'])): ?>
                        <div class="video-description">
                            <?php echo $currentItem['description']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Item Navigation -->
                <?php if (count($items) > 1): ?>
                <div class="item-nav">
                    <!-- Previous Item -->
                    <button class="item-nav-btn" onclick="prevItem()" <?php echo $currentIndex <= 0 ? 'disabled' : ''; ?>>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.93631 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.58 4.83001L3.33 10.08C3.11929 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11929 11.4591 3.33 11.67L8.58 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.93631 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.84 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3435 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.84L10.17 6.42001Z" fill="#FEFFFF"/>
                        </svg>
                        <span class="d-none d-md-inline">Previous</span>
                    </button>

                    <!-- Item Counter -->
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="item-counter"><?php echo $currentIndex + 1; ?></div>
                        <span style="color: rgba(255, 255, 255, 0.60); font-size: 16px;">/</span>
                        <div class="item-counter"><?php echo count($items); ?></div>
                    </div>

                    <!-- Next Item -->
                    <button class="item-nav-btn" onclick="nextItem()" <?php echo $currentIndex >= count($items) - 1 ? 'disabled' : ''; ?>>
                        <span class="d-none d-md-inline">Next</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                        </svg>
                    </button>
                </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center">No content available</div>
        <?php endif; ?>
    </div>
</div>

<script>
const items = <?php echo json_encode($items); ?>;
let currentIndex = <?php echo $currentIndex; ?>;
const userId = '<?php echo $userId; ?>';
const chapterId = '<?php echo $chapterId; ?>';
const parentId = '<?php echo $parentId; ?>';

function prevItem() {
    if (currentIndex <= 0) return;
    const prevItem = items[currentIndex - 1];
    window.location.href = `watch-learn-view.php?id=${chapterId}&itemId=${prevItem.id}&parentId=${parentId}`;
}

function nextItem() {
    if (currentIndex >= items.length - 1) return;
    const nextItem = items[currentIndex + 1];
    window.location.href = `watch-learn-view.php?id=${chapterId}&itemId=${nextItem.id}&parentId=${parentId}`;
}

function toggleBookmark() {
    const currentItem = items[currentIndex];
    if (!currentItem) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('watch_learn_id', currentItem.id);
    
    fetch('/bookmark-proxy.php?module=watch-learn', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('bookmarkBtn').classList.toggle('active');
        alert(data.message || 'Bookmark updated');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update bookmark');
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
