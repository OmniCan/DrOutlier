<?php
/**
 * Bookmarks Page - Saved Items
 * View all bookmarked content across modules
 */

session_start();

// Include helpers
require_once __DIR__ . '/includes/api-helper.php';

// Check authentication
requireAuth();

$pageTitle = 'My Bookmarks - Dr. Outlier';
$user = getUser();
$token = getToken();
$userId = $_SESSION['user']['id'] ?? ($_SESSION['user_id'] ?? '');

// Fetch all bookmarks
$loading = true;
$bookmarks = [
    'notes' => [],
    'spotters' => [],
    'osce' => [],
    'quizora' => [],
    'aiRad' => [],
    'practicalEssentials' => [],
    'watchAndLearn' => []
];

// Fetch Notes bookmarks (new theory notes)
$notesResponse = apiRequest('/theory-notes/get-bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($notesResponse['data']['list']['data'])) {
    $bookmarks['notes'] = $notesResponse['data']['list']['data'];
} elseif (isset($notesResponse['data']['list']) && is_array($notesResponse['data']['list'])) {
    $bookmarks['notes'] = $notesResponse['data']['list'];
}

// Fetch Spotters bookmarks (new spotters)
$spottersResponse = apiRequest('/new-spotters/get-bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($spottersResponse['data']['list']['data'])) {
    $bookmarks['spotters'] = $spottersResponse['data']['list']['data'];
} elseif (isset($spottersResponse['data']['list']) && is_array($spottersResponse['data']['list'])) {
    $bookmarks['spotters'] = $spottersResponse['data']['list'];
}

// Fetch OSCE bookmarks (new osce)
$osceResponse = apiRequest('/new-osce/get-bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($osceResponse['data']['list']['data'])) {
    $bookmarks['osce'] = $osceResponse['data']['list']['data'];
} elseif (isset($osceResponse['data']['list']) && is_array($osceResponse['data']['list'])) {
    $bookmarks['osce'] = $osceResponse['data']['list'];
}

// Fetch Quizora bookmarks
$quizoraResponse = apiRequest('/quiz/bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($quizoraResponse['data']['list']['data'])) {
    $bookmarks['quizora'] = $quizoraResponse['data']['list']['data'];
} elseif (isset($quizoraResponse['data']) && is_array($quizoraResponse['data'])) {
    $bookmarks['quizora'] = $quizoraResponse['data'];
}

// Fetch AI-Rad bookmarks (new exam cases)
$aiRadResponse = apiRequest('/new-exam-cases/get-bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($aiRadResponse['data']['list']['data'])) {
    $bookmarks['aiRad'] = $aiRadResponse['data']['list']['data'];
} elseif (isset($aiRadResponse['data']['list']) && is_array($aiRadResponse['data']['list'])) {
    $bookmarks['aiRad'] = $aiRadResponse['data']['list'];
}

// Fetch Practical Essentials bookmarks (new table viva)
$practicalResponse = apiRequest('/new-table-viva/get-bookmarks', 'POST', ['user_id' => $userId], $token);
if (isset($practicalResponse['data']['list']['data'])) {
    $bookmarks['practicalEssentials'] = $practicalResponse['data']['list']['data'];
} elseif (isset($practicalResponse['data']['list']) && is_array($practicalResponse['data']['list'])) {
    $bookmarks['practicalEssentials'] = $practicalResponse['data']['list'];
}

// Fetch Watch & Learn bookmarks
$watchResponse = apiRequest('/watch-and-learn-category/get-watch-bookmark', 'POST', ['user_id' => $userId], $token);
if (isset($watchResponse['data']['list']['data'])) {
    $bookmarks['watchAndLearn'] = $watchResponse['data']['list']['data'];
} elseif (isset($watchResponse['data']['list']) && is_array($watchResponse['data']['list'])) {
    $bookmarks['watchAndLearn'] = $watchResponse['data']['list'];
}

$loading = false;

// Include header
include __DIR__ . '/includes/header.php';
?>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 20px;
}

.nav-tabs {
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 30px;
}

.nav-tabs .nav-link {
    color: rgba(255, 255, 255, 0.6);
    border: none;
    padding: 12px 20px;
    margin-right: 10px;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.05);
}

.nav-tabs .nav-link.active {
    color: white;
    background: linear-gradient(135deg, #126E97 0%, #0d5070 100%);
    border-bottom: 3px solid #126E97;
}

.bookmark-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.bookmark-card:hover {
    border-color: #126E97;
    transform: translateX(5px);
}

.tab-content {
    min-height: 300px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: rgba(255, 255, 255, 0.2);
    margin-bottom: 20px;
}
</style>

<!-- Main Content -->
<div class="main-wrapper" style="background: #1B1E27; min-height: 100vh; padding: 60px 0;">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-white mb-2" style="font-size: 32px; font-weight: 700;">
                    <i class="fas fa-bookmark me-3" style="color: #126E97;"></i>
                    My Bookmarks
                </h1>
                <p style="color: rgba(255, 255, 255, 0.70); font-size: 16px;">
                    Access all your saved content in one place
                </p>
            </div>
        </div>

        <?php if ($loading): ?>
        <!-- Loader -->
        <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <?php else: ?>
        
        <!-- Bookmarks Tabs -->
        <div class="glass-card">
            <ul class="nav nav-tabs" id="bookmarkTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#notes-tab">
                        <i class="fas fa-book me-2"></i>Notes
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['notes']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#spotters-tab">
                        <i class="fas fa-image me-2"></i>Spotters
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['spotters']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#osce-tab">
                        <i class="fas fa-clipboard-check me-2"></i>OSCE
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['osce']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#quizora-tab">
                        <i class="fas fa-question-circle me-2"></i>Quizora
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['quizora']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#airad-tab">
                        <i class="fas fa-brain me-2"></i>AI-Rad
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['aiRad']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#practical-tab">
                        <i class="fas fa-tools me-2"></i>Practical
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['practicalEssentials']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#watch-tab">
                        <i class="fas fa-play-circle me-2"></i>Watch & Learn
                        <span class="badge bg-primary ms-2"><?php echo count($bookmarks['watchAndLearn']); ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Notes Tab -->
                <div class="tab-pane fade show active" id="notes-tab">
                    <?php if (empty($bookmarks['notes'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No notes bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['notes'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Notes'); ?>
                                    </p>
                                </div>
                                <a href="/notes-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Spotters Tab -->
                <div class="tab-pane fade" id="spotters-tab">
                    <?php if (empty($bookmarks['spotters'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No spotters bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['spotters'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Spotters'); ?>
                                    </p>
                                </div>
                                <a href="/spotters-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- OSCE Tab -->
                <div class="tab-pane fade" id="osce-tab">
                    <?php if (empty($bookmarks['osce'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No OSCE bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['osce'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'OSCE'); ?>
                                    </p>
                                </div>
                                <a href="/osce-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Quizora Tab -->
                <div class="tab-pane fade" id="quizora-tab">
                    <?php if (empty($bookmarks['quizora'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No quizzes bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['quizora'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['quiz_name'] ?? $item['name'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Quizora'); ?>
                                    </p>
                                </div>
                                <a href="/quizora-chapters.php?id=<?php echo urlencode($item['category_id'] ?? $item['id']); ?>" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- AI-Rad Tab -->
                <div class="tab-pane fade" id="airad-tab">
                    <?php if (empty($bookmarks['aiRad'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No AI-Rad bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['aiRad'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'AI-Rad'); ?>
                                    </p>
                                </div>
                                <a href="/ai-rad-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Practical Essentials Tab -->
                <div class="tab-pane fade" id="practical-tab">
                    <?php if (empty($bookmarks['practicalEssentials'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No practical essentials bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['practicalEssentials'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Practical Essentials'); ?>
                                    </p>
                                </div>
                                <a href="/practical-essentials-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Watch & Learn Tab -->
                <div class="tab-pane fade" id="watch-tab">
                    <?php if (empty($bookmarks['watchAndLearn'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p class="text-white-50">No videos bookmarked yet</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($bookmarks['watchAndLearn'] as $item): ?>
                        <div class="bookmark-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-white mb-2"><?php echo htmlspecialchars($item['name'] ?? $item['title'] ?? 'Untitled'); ?></h6>
                                    <p class="text-white-50 mb-0" style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Watch & Learn'); ?>
                                    </p>
                                </div>
                                <a href="/watch-learn-view.php?id=<?php echo urlencode($item['category'] ?? ''); ?>&itemId=<?php echo urlencode($item['id'] ?? ''); ?>&parentId=0" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
