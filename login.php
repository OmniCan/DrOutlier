<?php
/**
 * Login Page
 * Authenticates users via Laravel API
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_token'])) {
    header('Location: /');
    exit;
}

require_once __DIR__ . '/includes/api-helper.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Call login API
        $response = apiRequest('/login', 'POST', [
            'email' => $email,
            'password' => $password
        ]);
        
        if (isset($response['error'])) {
            $error = $response['error'];
        } elseif (isset($response['success']) && $response['success']) {
            // Login successful
            $_SESSION['user_token'] = $response['data']['token'] ?? '';
            $_SESSION['user'] = $response['data']['user'] ?? [];
            $_SESSION['user_id'] = $response['data']['user']['id'] ?? '';
            
            header('Location: /');
            exit;
        } else {
            $error = $response['message'] ?? 'Login failed. Please try again.';
        }
    }
}

$pageTitle = 'Login - Dr. Outlier';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <!-- Login Form -->
    <div class="main-wrapper">
        <div class="container">
            <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
                <div class="col-lg-5 col-md-7">
                    <div class="login-box" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-radius: 20px; padding: 40px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-center mb-4">
                            <img src="/public/images/Header-Logo.webp" alt="Dr Outlier" style="max-width: 200px;" />
                            <h3 class="text-white mt-3">Welcome Back</h3>
                            <p class="text-white-50">Login to continue your learning journey</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label text-white">Email Address</label>
                                <input type="email" class="form-control" name="email" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="your@email.com" />
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Password</label>
                                <input type="password" class="form-control" name="password" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="••••••••" />
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" />
                                <label class="form-check-label text-white" for="remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3" style="background: linear-gradient(135deg, #126E97 0%, #1a8fc7 100%); border: none; padding: 12px; font-weight: 600;">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                            
                            <div class="text-center">
                                <p class="text-white-50 mb-2">Don't have an account? <a href="/register.php" class="text-primary" style="text-decoration: none; font-weight: 600;">Register here</a></p>
                                <a href="/forgot-password.php" class="text-white-50" style="text-decoration: none; font-size: 14px;">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
