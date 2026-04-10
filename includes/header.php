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
    <!-- Header -->
    <header class="header-wrapper">
        <div class="header-inner">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <div class="col-lg-4 col-2">
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar">
                            <img src="/public/images/Toggle.svg" class="img-fluid" alt="Toggle" />
                        </button>
                    </div>
                    
                    <div class="col-lg-4 col-7 text-center">
                        <a class="navbar-brand col-md-2" href="/">
                            <img src="/public/images/logo.png" alt="Dr Outlier Radiology" style="max-height: 50px; width: auto; object-fit: contain;" />
                        </a>
                    </div>

                    <div class="col-lg-4 col-2 d-flex justify-content-end">
                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="btn-group">
                                <div data-bs-toggle="dropdown" class="dropDown-wrap" style="cursor: pointer;" role="button" aria-expanded="false">
                                    <!-- For phone view - Avatar -->
                                    <div class="d-lg-none">
                                        <img src="/public/images/avatar.png" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;" />
                                    </div>

                                    <!-- For large screens - Username -->
                                    <div class="d-none d-lg-block">
                                        <img alt="Login Logo" src="/public/images/login-logo.svg" />
                                        <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?>
                                    </div>

                                    <i class="fa-solid fa-chevron-down d-none d-lg-block"></i>
                                </div>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="/subscription.php" class="dropdown-item" style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-crown" style="color: #126E97;"></i>
                                            My Subscription
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" style="border-color: rgba(255, 255, 255, 0.1);" /></li>
                                    <li>
                                        <a href="/profile.php" class="dropdown-item" style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-user-circle" style="color: #126E97;"></i>
                                            Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/bookmarks.php" class="dropdown-item" style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-bookmark" style="color: #126E97;"></i>
                                            Saved / Bookmarks
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" style="border-color: rgba(255, 255, 255, 0.1);" /></li>
                                    <li>
                                        <a href="/logout.php" class="dropdown-item" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-sign-out-alt" style="color: #FF5252;"></i>
                                            Log Out
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

                    <!-- Offcanvas Menu -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDarkNavbar">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title">
                                <span>Welcome to <strong>Dr. Outlier</strong></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" href="/">Home <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/theory-notes.php">Theory Notes <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/spotters.php">Spotters <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/osce.php">OSCE <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/ai-rad.php">AI-Rad <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/practical-essentials.php">Practical Essentials <i class="fa-solid fa-chevron-right"></i></a></li>
                                <li class="nav-item"><a class="nav-link" href="/watch-learn.php">Watch &amp; Learn <i class="fa-solid fa-chevron-right"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
