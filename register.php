<?php
/**
 * Register Page
 * Creates new user account via Laravel API
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
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Call register API
        $response = apiRequest('/register', 'POST', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmPassword,
            'phone' => $phone
        ]);
        
        if (isset($response['error'])) {
            $error = $response['error'];
        } elseif (isset($response['success']) && $response['success']) {
            // Registration successful
            $success = 'Registration successful! Redirecting to login...';
            header('Refresh: 2; url=/login.php');
        } else {
            $error = $response['message'] ?? 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = 'Register - Dr. Outlier';
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
    <!-- Register Form -->
    <div class="main-wrapper">
        <div class="container">
            <div class="row justify-content-center" style="min-height: 100vh; align-items: center; padding: 40px 0;">
                <div class="col-lg-5 col-md-7">
                    <div class="login-box" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-radius: 20px; padding: 40px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-center mb-4">
                            <img src="/public/images/Header-Logo.webp" alt="Dr Outlier" style="max-width: 200px;" />
                            <h3 class="text-white mt-3">Create Account</h3>
                            <p class="text-white-50">Join Dr. Outlier and start learning</p>
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
                                <label class="form-label text-white">Full Name *</label>
                                <input type="text" class="form-control" name="name" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="John Doe" />
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Email Address *</label>
                                <input type="email" class="form-control" name="email" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="your@email.com" />
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="+91 1234567890" />
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Password *</label>
                                <input type="password" class="form-control" name="password" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="••••••••" />
                                <small class="text-white-50">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Confirm Password *</label>
                                <input type="password" class="form-control" name="confirm_password" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;" placeholder="••••••••" />
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3" style="background: linear-gradient(135deg, #126E97 0%, #1a8fc7 100%); border: none; padding: 12px; font-weight: 600;">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                            
                            <div class="text-center">
                                <p class="text-white-50 mb-0">Already have an account? <a href="/login.php" class="text-primary" style="text-decoration: none; font-weight: 600;">Login here</a></p>
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
