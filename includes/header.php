<?php
/**
 * Header Include - Reusable Navigation
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dr. Outlier Radiology'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <?php if (isset($additionalCSS)) echo $additionalCSS; ?>
</head>
<body>
<?php
$currentPath = strtok($_SERVER['REQUEST_URI'], '?');
function isActive($path) {
    global $currentPath;
    if ($path === '/' || $path === '/index.php') {
        return $currentPath === '/' || $currentPath === '/index.php';
    }
    return strpos($currentPath, $path) === 0;
}
$navItems = [
    ['url' => '/theory-notes.php', 'title' => 'Theory Notes',        'icon' => 'fa-solid fa-book'],
    ['url' => '/spotters.php',     'title' => 'Spotters',            'icon' => 'fa-solid fa-image'],
    ['url' => '/osce.php',         'title' => 'OSCE',                'icon' => 'fa-solid fa-stethoscope'],
    ['url' => '/ai-rad.php',       'title' => 'AI-Rad',              'icon' => 'fa-solid fa-brain'],
    ['url' => '/practical-essentials.php', 'title' => 'Practical Essentials', 'icon' => 'fa-solid fa-clipboard-check'],
    ['url' => '/watch-learn.php',  'title' => 'Watch &amp; Learn',   'icon' => 'fa-solid fa-video'],
];
$savedItems = [
    ['url' => '/bookmarks.php?tab=spotters',   'title' => 'Saved Spotters',              'icon' => 'fa-solid fa-image'],
    ['url' => '/bookmarks.php?tab=notes',      'title' => 'Saved Notes',                 'icon' => 'fa-solid fa-book'],
    ['url' => '/bookmarks.php?tab=osce',       'title' => 'Saved OSCE',                  'icon' => 'fa-solid fa-stethoscope'],
    ['url' => '/bookmarks.php?tab=ai-rad',     'title' => 'Saved AI-Rad',                'icon' => 'fa-solid fa-brain'],
    ['url' => '/bookmarks.php?tab=practical',  'title' => 'Saved Practical Essentials',  'icon' => 'fa-solid fa-clipboard-check'],
    ['url' => '/bookmarks.php?tab=watch',      'title' => 'Saved Watch &amp; Learn',     'icon' => 'fa-solid fa-video'],
];
$isLoggedIn = isset($_SESSION['user']);
$userName   = htmlspecialchars($_SESSION['user']['name'] ?? 'User');
?>
    <!-- Header -->
    <header class="header-wrapper">
        <div class="header-inner">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <div class="col-lg-4 col-2">
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
                            <img src="/public/images/Toggle.svg" class="img-fluid" alt="Toggle" />
                        </button>
                    </div>

                    <div class="col-lg-4 col-7 text-center">
                        <a class="navbar-brand col-md-2" href="/">
                            <img src="/public/images/Header-Logo.webp" class="img-fluid" alt="Dr Outlier Radiology" />
                        </a>
                    </div>

                    <div class="col-lg-4 col-2 d-flex justify-content-end">
                        <?php if ($isLoggedIn): ?>
                            <div class="btn-group">
                                <div data-bs-toggle="dropdown" class="dropDown-wrap" style="cursor: pointer;" role="button" aria-expanded="false">
                                    <div class="d-lg-none">
                                        <img src="/public/images/login-logo.svg" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%;" />
                                    </div>
                                    <div class="d-none d-lg-block">
                                        <img alt="Login Logo" src="/public/images/login-logo.svg" />
                                        <?php echo $userName; ?>
                                    </div>
                                    <i class="fa-solid fa-chevron-down d-none d-lg-block"></i>
                                </div>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="/subscription.php" class="dropdown-item" style="display:flex;align-items:center;gap:8px;">
                                            <i class="fas fa-crown" style="color:#126E97;"></i> My Subscription
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.1);" /></li>
                                    <li>
                                        <a href="/profile.php" class="dropdown-item" style="display:flex;align-items:center;gap:8px;">
                                            <i class="fas fa-user-circle" style="color:#126E97;"></i> Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/bookmarks.php" class="dropdown-item" style="display:flex;align-items:center;gap:8px;">
                                            <i class="fas fa-bookmark" style="color:#126E97;"></i> Saved / Bookmarks
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.1);" /></li>
                                    <li>
                                        <a href="/logout.php" class="dropdown-item" style="display:flex;align-items:center;gap:8px;">
                                            <i class="fas fa-sign-out-alt" style="color:#FF5252;"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-link loginBtn d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fa-solid fa-user"></i> Login
                            </button>
                        <?php endif; ?>

                        <button class="btn btn-link searchBtn d-lg-none d-block">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>

                    <!-- Search Overlay (mobile) -->
                    <div class="overlay"></div>
                    <div class="search-panel">
                        <div class="search-top-wrapper">
                            <div class="row align-items-center">
                                <div class="col-4"><i class="fa-solid fa-chevron-left closeBtn"></i></div>
                                <div class="col-4"><h6>Search</h6></div>
                                <div class="col-4"></div>
                            </div>
                        </div>
                        <div class="search-bottom-wrapper">
                            <input type="text" id="mobileSearchInput" class="form-control mb-4" placeholder="Search here..." autocomplete="off" />
                            <div class="taber-wrapper">
                                <ul class="nav nav-tabs" id="mobileSearchTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mob-notes" type="button">Notes</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mob-spotters" type="button">Spotters</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mob-osce" type="button">OSCE</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mob-airad" type="button">AI-Rad</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mob-watch" type="button">Watch And Learn</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mob-practical" type="button">Practical Essentials</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="mobileSearchTabContent">
                                    <div class="tab-pane fade show active" id="mob-notes" role="tabpanel"><ul class="mob-search-results" data-module="notes" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                    <div class="tab-pane fade" id="mob-spotters" role="tabpanel"><ul class="mob-search-results" data-module="spotters" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                    <div class="tab-pane fade" id="mob-osce" role="tabpanel"><ul class="mob-search-results" data-module="osce" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                    <div class="tab-pane fade" id="mob-airad" role="tabpanel"><ul class="mob-search-results" data-module="munchies" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                    <div class="tab-pane fade" id="mob-watch" role="tabpanel"><ul class="mob-search-results" data-module="whatchandlearn" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                    <div class="tab-pane fade" id="mob-practical" role="tabpanel"><ul class="mob-search-results" data-module="basics" style="overflow:auto;"></ul><p class="no-results-msg">No results found</p></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Offcanvas Sidebar -->
                    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel"
                         style="width:85%;max-width:320px;min-width:280px;background:linear-gradient(180deg,#1B1E27 0%,#0F1116 100%);box-shadow:-4px 0 20px rgba(0,0,0,0.5);">
                        <div class="offcanvas-header" style="border-bottom:1px solid rgba(255,255,255,0.1);padding:20px 24px;">
                            <div style="width:100%;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0" style="color:white;font-size:18px;font-weight:600;font-family:Poppins;">Menu</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close" style="font-size:14px;opacity:0.8;"></button>
                                </div>
                                <div class="heading-wrap">
                                    <h6 style="color:rgba(255,255,255,0.7);font-size:13px;font-weight:400;margin:0;font-family:Poppins;">
                                        Welcome to <span style="color:#44A6C5;font-weight:600;">Dr Outlier Radiology</span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="offcanvas-body" style="padding:16px 0;overflow-y:auto;">

                            <!-- Login/Logout button (mobile only) -->
                            <div class="d-block d-md-none px-4 mb-3">
                                <?php if (!$isLoggedIn): ?>
                                    <button class="btn w-100" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="offcanvas"
                                            style="background:linear-gradient(92.48deg,#44A6C5 3.13%,#1E4FFD 100%);color:white;border-radius:10px;padding:10px 20px;font-size:15px;font-weight:500;border:none;font-family:Poppins;">
                                        <i class="fa-solid fa-right-to-bracket me-2"></i> Login
                                    </button>
                                <?php else: ?>
                                    <a href="/logout.php" class="btn w-100"
                                       style="background:rgba(255,59,48,0.1);color:#FF3B30;border-radius:10px;padding:10px 20px;font-size:15px;font-weight:500;border:1px solid rgba(255,59,48,0.3);font-family:Poppins;">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Main nav (mobile) -->
                            <ul class="navbar-nav d-block d-md-none" style="padding:0 12px;">
                                <?php
                                $homeActive = isActive('/');
                                $homeStyle  = $homeActive
                                    ? 'color:#44A6C5;background:rgba(68,166,197,0.15);font-weight:600;'
                                    : 'color:rgba(255,255,255,0.85);background:transparent;font-weight:400;';
                                ?>
                                <li class="nav-item" style="margin-bottom:4px;">
                                    <a class="nav-link" href="/" data-bs-dismiss="offcanvas"
                                       style="<?php echo $homeStyle; ?>padding:12px 16px;border-radius:10px;font-size:15px;font-family:Poppins;display:flex;align-items:center;transition:all 0.2s ease;">
                                        <i class="fa-solid fa-house" style="margin-right:12px;font-size:16px;"></i> Home
                                    </a>
                                </li>
                                <?php foreach ($navItems as $item):
                                    $active = isActive($item['url']);
                                    $style  = $active
                                        ? 'color:#44A6C5;background:rgba(68,166,197,0.15);font-weight:600;'
                                        : 'color:rgba(255,255,255,0.85);background:transparent;font-weight:400;';
                                ?>
                                <li class="nav-item" style="margin-bottom:4px;">
                                    <a class="nav-link" href="<?php echo $item['url']; ?>" data-bs-dismiss="offcanvas"
                                       style="<?php echo $style; ?>padding:12px 16px;border-radius:10px;font-size:15px;font-family:Poppins;display:flex;align-items:center;transition:all 0.2s ease;">
                                        <i class="<?php echo $item['icon']; ?>" style="margin-right:12px;font-size:16px;"></i>
                                        <?php echo $item['title']; ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>

                                <!-- Saved dropdown (mobile) -->
                                <li class="nav-item dropdown" style="margin-top:8px;margin-bottom:4px;">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                       style="color:rgba(255,255,255,0.85);padding:12px 16px;border-radius:10px;font-size:15px;font-weight:400;font-family:Poppins;display:flex;align-items:center;">
                                        <i class="fa-solid fa-bookmark" style="margin-right:12px;font-size:16px;"></i> Saved
                                    </a>
                                    <ul class="dropdown-menu" style="background:#252A3A;border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:8px;margin-top:4px;">
                                        <?php foreach ($savedItems as $s): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo $s['url']; ?>" data-bs-dismiss="offcanvas"
                                               style="color:rgba(255,255,255,0.85);padding:10px 14px;border-radius:8px;font-size:14px;font-family:Poppins;">
                                                <i class="<?php echo $s['icon']; ?>" style="margin-right:10px;font-size:14px;"></i>
                                                <?php echo $s['title']; ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            </ul>

                            <!-- Desktop nav (inside offcanvas) -->
                            <div class="d-none d-md-block">
                                <div style="padding:0 16px;margin-top:16px;margin-bottom:12px;">
                                    <h6 style="color:rgba(255,255,255,0.5);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;font-family:Poppins;">Saved Items</h6>
                                </div>
                                <ul class="navbar-nav" style="padding:0 12px;">
                                    <?php foreach ($savedItems as $s): ?>
                                    <li class="nav-item" style="margin-bottom:4px;">
                                        <a class="nav-link" href="<?php echo $s['url']; ?>"
                                           style="color:rgba(255,255,255,0.85);padding:12px 16px;border-radius:10px;font-size:15px;font-weight:400;font-family:Poppins;display:flex;align-items:center;justify-content:space-between;transition:all 0.2s ease;">
                                            <span><i class="<?php echo $s['icon']; ?>" style="margin-right:12px;font-size:16px;"></i><?php echo $s['title']; ?></span>
                                            <i class="fa-solid fa-chevron-right" style="font-size:14px;"></i>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        </div>
                    </div><!-- /offcanvas -->

                </div>
            </nav>

            <!-- Desktop second nav bar (header-menu) -->
            <div class="header-menu">
                <nav class="navbar navbar-expand-md d-lg-flex d-none">
                    <div class="container">
                        <ul class="navbar-nav justify-content-between w-100 align-items-center">
                            <li class="nav-item">
                                <a class="nav-link <?php echo isActive('/') ? 'active' : ''; ?>" href="/">Home</a>
                            </li>
                            <?php foreach ($navItems as $item): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isActive($item['url']) ? 'active' : ''; ?>" href="<?php echo $item['url']; ?>">
                                    <?php echo $item['title']; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            <li class="nav-item">
                                <div class="search dropdown">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search" id="desktopSearchInput" placeholder="Search" class="form-control" autocomplete="off"
                                           data-bs-toggle="dropdown" aria-expanded="false" />
                                    <ul class="dropdown-menu w-100 custom-dropdown searchhhh" id="desktopSearchResults">
                                        <li><span class="dropdown-item text-muted">Type to search...</span></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

        </div>
    </header>

    <script>
    (function() {
        // ── Search helpers ──────────────────────────────────────────
        function saveToSession(data) {
            try {
                localStorage.setItem('selectedData', JSON.stringify([data]));
            } catch(e) {}
        }

        function renderSearchResults(data, container) {
            container.innerHTML = '';
            const modules = [
                { key: 'notes',           label: 'Notes' },
                { key: 'spotters',        label: 'Spotters' },
                { key: 'osce',            label: 'OSCE' },
                { key: 'munchies',        label: 'AI-Rad' },
                { key: 'whatchandlearn',  label: 'Watch And Learn' },
                { key: 'basics',          label: 'Practical Essentials' },
            ];
            let hasResults = false;
            modules.forEach(function(m) {
                if (data[m.key] && data[m.key].length > 0) {
                    hasResults = true;
                    data[m.key].forEach(function(item) {
                        var li = document.createElement('li');
                        li.innerHTML = '<a href="/search-result.php" class="dropdown-item" data-item=\'' +
                            JSON.stringify(item).replace(/'/g, '&#39;') + '\'>' +
                            '<h6 class="title">' + (item.title || '') + '</h6>' +
                            '<p class="module text-muted">' + m.label + '</p></a>';
                        li.querySelector('a').addEventListener('click', function() {
                            saveToSession(item);
                        });
                        container.appendChild(li);
                    });
                }
            });
            if (!hasResults) {
                var li = document.createElement('li');
                li.innerHTML = '<span class="dropdown-item text-muted">No results found</span>';
                container.appendChild(li);
            }
        }

        function debounce(fn, delay) {
            var t;
            return function() { clearTimeout(t); t = setTimeout(fn.bind(this, ...arguments), delay); };
        }

        function doSearch(query, callback) {
            if (!query || query.length < 2) return;
            fetch('/search-proxy.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'title=' + encodeURIComponent(query)
            })
            .then(function(r) { return r.json(); })
            .then(function(d) { callback(d && d.data && d.data.datalist ? d.data.datalist : {}); })
            .catch(function() {});
        }

        // Desktop search
        var desktopInput   = document.getElementById('desktopSearchInput');
        var desktopResults = document.getElementById('desktopSearchResults');
        if (desktopInput && desktopResults) {
            desktopInput.addEventListener('input', debounce(function(e) {
                doSearch(e.target.value, function(data) {
                    renderSearchResults(data, desktopResults);
                });
            }, 400));
        }

        // Mobile search
        var mobileInput = document.getElementById('mobileSearchInput');
        if (mobileInput) {
            mobileInput.addEventListener('input', debounce(function(e) {
                var q = e.target.value;
                doSearch(q, function(data) {
                    document.querySelectorAll('.mob-search-results').forEach(function(ul) {
                        var mod = ul.getAttribute('data-module');
                        ul.innerHTML = '';
                        var items = data[mod] || [];
                        if (items.length > 0) {
                            items.forEach(function(item) {
                                var li = document.createElement('li');
                                li.style.listStyle = 'none';
                                li.innerHTML = '<a href="/search-result.php" class="dropdown-item" style="color:black;">' +
                                    '<h6 class="title" style="color:black;">' + (item.title || '') + '</h6>' +
                                    '<p class="module text-muted">' + (mod) + '</p></a>';
                                li.querySelector('a').addEventListener('click', function() { saveToSession(item); });
                                ul.appendChild(li);
                            });
                            ul.closest('.tab-pane').querySelector('.no-results-msg').style.display = 'none';
                        } else {
                            ul.closest('.tab-pane').querySelector('.no-results-msg').style.display = '';
                        }
                    });
                });
            }, 400));
        }
    })();
    </script>
