<?php
/**
 * AI-Rad - PDF Viewer Page
 * Displays PDF content with navigation and bookmarks
 */

session_start();
require_once __DIR__ . '/includes/api-helper.php';
requireAuth();

$pageTitle = 'AI-Rad - Dr. Outlier';
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
    $response = apiRequest('/api/new-exam-cases/items-by-chapter', 'POST', ['chapter_id' => $chapterId], $token);
    
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

$additionalCSS = <<<CSS
<style>
    body { background: #1B1E27; }
    .pdf-viewer-container { background: #1B1A1A; border-radius: 24px; overflow: hidden; margin: 30px auto; max-width: 1400px; }
    .pdf-canvas-wrapper { background: #2A2A2A; display: flex; justify-content: center; align-items: center; min-height: 600px; padding: 40px; overflow: auto; }
    .pdf-controls { background: #1B1A1A; border-top: 1px solid #126E97; padding: 20px 30px; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
    .control-btn { padding: 10px 20px; background: linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%); border: none; border-radius: 12px; color: white; font-size: 14px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
    .control-btn:disabled { background: rgba(255,255,255,0.1); cursor: not-allowed; opacity: 0.5; }
    .page-counter { color: white; font-size: 16px; font-weight: 500; padding: 10px 20px; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
    .zoom-btn { padding: 10px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; cursor: pointer; }
    .zoom-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .bookmark-btn { padding: 8px 12px; background: white; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.60); cursor: pointer; transition: all 0.3s; position: absolute; top: 20px; right: 20px; z-index: 1000; }
    .bookmark-btn.active svg path { fill: url(#bookmarkGradient); }
    .item-nav { background: #1B1A1A; padding: 20px; display: flex; align-items: center; justify-content: center; gap: 20px; }
    .item-nav-btn { padding: 8px 38px; display: flex; align-items: center; gap: 12px; cursor: pointer; background: rgba(255, 255, 255, 0.05); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.3s; }
    .item-nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .item-counter { background: rgba(0, 0, 0, 0.60); border-radius: 12px; border: 1px solid #1B1A1A; padding: 7px 15px; color: white; font-size: 16px; }
    .category-title-bar { width: 100%; height: 62px; background: #282D41; display: flex; align-items: center; justify-content: center; margin-bottom: 0; }
    .category-title-bar h2 { color: white; font-size: 26px; font-weight: 700; margin: 0; }
    .loader-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999; }
    .spinner { width: 50px; height: 50px; border: 4px solid rgba(68, 166, 197, 0.3); border-top: 4px solid #44A6C5; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
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
            <div class="pdf-viewer-container">
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
                <div class="pdf-canvas-wrapper" id="pdfContainer">
                    <canvas id="pdfCanvas"></canvas>
                </div>
                <div class="pdf-controls" id="pdfControls" style="display: none;">
                    <button class="control-btn" id="prevPageBtn" onclick="prevPage()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.9363 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.57999 4.83001L3.32999 10.08C3.11931 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11931 11.4591 3.32999 11.67L8.57999 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.9363 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.83999 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3434 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.83999L10.17 6.42001Z" fill="#FEFFFF"/>
                        </svg>
                        <span class="d-none d-md-inline">Previous Page</span>
                    </button>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="page-counter" id="pageCounter">1 / 1</div>
                        <button class="zoom-btn" id="zoomOutBtn" onclick="zoomOut()">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.9785 8.51779H11.0571M16.7142 16.7142L13.9551 13.9551M8.53707 15.7885C13.1785 15.7885 15.7885 13.1785 15.7885 8.53707C15.7885 3.89564 13.1785 1.28564 8.53707 1.28564C3.89564 1.28564 1.28564 3.89564 1.28564 8.53707C1.28564 13.1785 3.89564 15.7885 8.53707 15.7885Z" stroke="#FEFFFF" stroke-width="1.28571" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="zoom-btn" id="zoomInBtn" onclick="zoomIn()">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.53707 5.9965V11.0764M5.9965 8.53707H11.0764M16.7142 16.7142L13.9551 13.9551M8.53707 15.7885C13.1785 15.7885 15.7885 13.1785 15.7885 8.53707C15.7885 3.89564 13.1785 1.28564 8.53707 1.28564C3.89564 1.28564 1.28564 3.89564 1.28564 8.53707C1.28564 13.1785 3.89564 15.7885 8.53707 15.7885Z" stroke="#FEFFFF" stroke-width="1.28571" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <button class="control-btn" id="nextPageBtn" onclick="nextPage()">
                        <span class="d-none d-md-inline">Next Page</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                        </svg>
                    </button>
                </div>
                <?php if (count($items) > 1): ?>
                <div class="item-nav">
                    <button class="item-nav-btn" onclick="prevItem()" <?php echo $currentIndex <= 0 ? 'disabled' : ''; ?>>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.93631 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.58 4.83001L3.33 10.08C3.11929 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11929 11.4591 3.33 11.67L8.58 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.93631 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.84 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3435 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.84L10.17 6.42001Z" fill="#FEFFFF"/>
                        </svg>
                        <span class="d-none d-md-inline">Previous</span>
                    </button>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="item-counter"><?php echo $currentIndex + 1; ?></div>
                        <span style="color: rgba(255, 255, 255, 0.60); font-size: 16px;">/</span>
                        <div class="item-counter"><?php echo count($items); ?></div>
                    </div>
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

<div class="loader-overlay" id="loader" style="display: none;"><div class="spinner"></div></div>

<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
const items = <?php echo json_encode($items); ?>;
let currentIndex = <?php echo $currentIndex; ?>;
const userId = '<?php echo $userId; ?>';
const chapterId = '<?php echo $chapterId; ?>';
const parentId = '<?php echo $parentId; ?>';
let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null, scale = 1.2;
const canvas = document.getElementById('pdfCanvas'), ctx = canvas.getContext('2d');

function loadPDF() {
    const currentItem = items[currentIndex];
    if (!currentItem || !currentItem.pdf_file) {
        document.getElementById('pdfContainer').innerHTML = '<p style="color: white; text-align: center;">No PDF available</p>';
        return;
    }
    const pdfUrl = 'https://admin.droutlier.com/assets/exam_case_pdf/' + currentItem.pdf_file;
    document.getElementById('loader').style.display = 'flex';
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        pdfDoc = pdf;
        document.getElementById('loader').style.display = 'none';
        document.getElementById('pdfControls').style.display = 'flex';
        renderPage(pageNum);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        document.getElementById('loader').style.display = 'none';
        document.getElementById('pdfContainer').innerHTML = '<p style="color: white; text-align: center;">Error loading PDF</p>';
    });
}

function renderPage(num) {
    pageRendering = true;
    pdfDoc.getPage(num).then(function(page) {
        const viewport = page.getViewport({ scale: scale });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function() {
            pageRendering = false;
            if (pageNumPending !== null) { renderPage(pageNumPending); pageNumPending = null; }
            updatePageCounter(); updateButtons();
        });
    });
}

function queueRenderPage(num) { if (pageRendering) { pageNumPending = num; } else { renderPage(num); } }
function prevPage() { if (pageNum <= 1) return; pageNum--; queueRenderPage(pageNum); }
function nextPage() { if (pageNum >= pdfDoc.numPages) return; pageNum++; queueRenderPage(pageNum); }
function zoomIn() { if (scale >= 3) return; scale += 0.2; queueRenderPage(pageNum); }
function zoomOut() { if (scale <= 0.5) return; scale -= 0.2; queueRenderPage(pageNum); }
function updatePageCounter() { if (pdfDoc) document.getElementById('pageCounter').textContent = pageNum + ' / ' + pdfDoc.numPages; }
function updateButtons() {
    document.getElementById('prevPageBtn').disabled = pageNum <= 1;
    document.getElementById('nextPageBtn').disabled = pdfDoc && pageNum >= pdfDoc.numPages;
    document.getElementById('zoomOutBtn').disabled = scale <= 0.5;
    document.getElementById('zoomInBtn').disabled = scale >= 3;
}

function prevItem() {
    if (currentIndex <= 0) return;
    window.location.href = `ai-rad-view.php?id=${chapterId}&itemId=${items[currentIndex - 1].id}&parentId=${parentId}`;
}

function nextItem() {
    if (currentIndex >= items.length - 1) return;
    window.location.href = `ai-rad-view.php?id=${chapterId}&itemId=${items[currentIndex + 1].id}&parentId=${parentId}`;
}

function toggleBookmark() {
    const currentItem = items[currentIndex];
    if (!currentItem) return;
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('exam_case_id', currentItem.id);
    fetch('https://admin.droutlier.com/api/new-exam-cases/toggle-bookmark', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer <?php echo $token; ?>' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('bookmarkBtn').classList.toggle('active');
        alert(data.message || 'Bookmark updated');
    })
    .catch(error => { console.error('Error:', error); alert('Failed to update bookmark'); });
}

if (items.length > 0) { loadPDF(); }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
