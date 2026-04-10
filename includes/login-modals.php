<?php
/**
 * Login Modal - Bootstrap Modal Component
 * Handles email/password login and Google OAuth
 */
?>

<!-- Login Modal -->
<div class="modal fade custom-modal" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <!-- Modal Header -->
            <div class="modal-header">
                <img src="/public/images/signup-logo.svg" class="img-fluid" alt="Signup Logo" style="max-width: 80px;" />
                <h2 class="modal-title">SIGN IN</h2>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <div id="login-error" class="alert alert-danger d-none"></div>
                <div id="login-success" class="alert alert-success d-none"></div>
                
                <form id="loginForm" method="POST">
                    <div class="mb-2 mt-3">
                        <input type="email" class="form-control" name="email" placeholder="E-mail/Phone Number" required />
                    </div>
                    
                    <div class="mb-2">
                        <input type="password" class="form-control" name="password" placeholder="Password" required />
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="remember" id="login-remember" />
                        <label class="form-check-label" for="login-remember">Remember me</label>
                        <a href="#" class="float-end" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="loginBtn">Sign In</button>
                    
                    <div class="bottom mt-3">
                        <span>New to Dr Outlier Radiology? 
                            <a href="#" class="bottom btn btn-primary" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
                        </span>
                    </div>
                    
                    <div class="mt-3 text-center"><span>or</span></div>
                    
                    <button type="button" class="btn btn-link googleBtn mt-3" id="googleLoginBtn">
                        <img src="/public/images/logos_google-icon.svg" class="img-fluid" alt="Google" /> Continue with Google
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Signup Modal -->
<div class="modal fade custom-modal" id="signupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="modal-header">
                <img src="/public/images/signup-logo.svg" class="img-fluid" alt="Signup Logo" style="max-width: 80px;" />
                <h2 class="modal-title">SIGN UP</h2>
            </div>
            
            <div class="modal-body">
                <div id="signup-error" class="alert alert-danger d-none"></div>
                <div id="signup-success" class="alert alert-success d-none"></div>
                
                <form id="signupForm" method="POST">
                    <div class="mb-2 mt-3">
                        <input type="text" class="form-control" name="name" placeholder="Full Name" required />
                    </div>
                    
                    <div class="mb-2">
                        <input type="email" class="form-control" name="email" placeholder="E-mail" required />
                    </div>
                    
                    <div class="mb-2">
                        <input type="tel" class="form-control" name="phone" placeholder="Phone Number (Optional)" />
                    </div>
                    
                    <div class="mb-2">
                        <input type="password" class="form-control" name="password" placeholder="Password (min 6 characters)" required minlength="6" />
                    </div>
                    
                    <div class="mb-2">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required />
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="signupBtn">Sign Up</button>
                    
                    <div class="bottom mt-3">
                        <span>Already have an account? 
                            <a href="#" class="bottom btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a>
                        </span>
                    </div>
                    
                    <div class="mt-3 text-center"><span>or</span></div>
                    
                    <button type="button" class="btn btn-link googleBtn mt-3" id="googleSignupBtn">
                        <img src="/public/images/logos_google-icon.svg" class="img-fluid" alt="Google" /> Continue with Google
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade custom-modal" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="modal-header">
                <img src="/public/images/signup-logo.svg" class="img-fluid" alt="Signup Logo" style="max-width: 80px;" />
                <h2 class="modal-title">FORGOT PASSWORD</h2>
            </div>
            
            <div class="modal-body">
                <div id="forgot-error" class="alert alert-danger d-none"></div>
                <div id="forgot-success" class="alert alert-success d-none"></div>
                
                <form id="forgotPasswordForm" method="POST">
                    <p class="text-white-50">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <div class="mb-3 mt-3">
                        <input type="email" class="form-control" name="email" placeholder="E-mail Address" required />
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="forgotBtn">Send Reset Link</button>
                    
                    <div class="bottom mt-3">
                        <span>Remember your password? 
                            <a href="#" class="bottom btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Modal Styles */
.custom-modal .modal-content {
    background: rgba(18, 26, 36, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    color: white;
}

.custom-modal .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-direction: column;
    align-items: center;
    padding: 30px 30px 20px;
}

.custom-modal .modal-title {
    color: white;
    font-size: 24px;
    font-weight: 600;
    margin-top: 15px;
}

.custom-modal .modal-body {
    padding: 30px;
}

.custom-modal .btn-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    color: white;
    opacity: 1;
    font-size: 24px;
    border: none;
}

.custom-modal .btn-close i {
    color: white;
}

.custom-modal .form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px 15px;
    border-radius: 8px;
}

.custom-modal .form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.custom-modal .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #126E97;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(18, 110, 151, 0.25);
}

.custom-modal .form-check-input {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

.custom-modal .form-check-label {
    color: rgba(255, 255, 255, 0.8);
}

.custom-modal .btn-primary {
    background: linear-gradient(135deg, #126E97 0%, #1a8fc7 100%);
    border: none;
    padding: 12px;
    width: 100%;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.custom-modal .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(18, 110, 151, 0.4);
}

.custom-modal .googleBtn {
    width: 100%;
    background: white;
    color: #333;
    padding: 12px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.custom-modal .googleBtn:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.custom-modal .bottom {
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

.custom-modal .bottom a {
    color: #126E97;
    text-decoration: none;
    font-weight: 600;
    display: inline;
    width: auto;
    padding: 0;
    background: none;
}

.custom-modal .bottom a:hover {
    color: #1a8fc7;
    text-decoration: underline;
    transform: none;
    box-shadow: none;
}

.custom-modal a.float-end {
    color: #126E97;
    text-decoration: none;
    font-size: 14px;
}

.custom-modal a.float-end:hover {
    color: #1a8fc7;
    text-decoration: underline;
}
</style>

<script>
// Login Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const forgotForm = document.getElementById('forgotPasswordForm');
    
    // Login Form Submit
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('loginBtn');
            const errorDiv = document.getElementById('login-error');
            const successDiv = document.getElementById('login-success');
            
            btn.disabled = true;
            btn.textContent = 'Signing In...';
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('https://admin.droutlier.com/api/login', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success' && data.data?.access_token) {
                    // Store session data via PHP
                    const loginResponse = await fetch('/login-handler.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            token: data.data.access_token,
                            user: data.data.user
                        })
                    });
                    
                    if (loginResponse.ok) {
                        successDiv.textContent = 'Login successful! Redirecting...';
                        successDiv.classList.remove('d-none');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    errorDiv.textContent = data.message || 'Login failed. Please check your credentials.';
                    errorDiv.classList.remove('d-none');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Sign In';
            }
        });
    }
    
    // Signup Form Submit
    if (signupForm) {
        signupForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('signupBtn');
            const errorDiv = document.getElementById('signup-error');
            const successDiv = document.getElementById('signup-success');
            
            const password = signupForm.querySelector('[name="password"]').value;
            const confirmPassword = signupForm.querySelector('[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                errorDiv.textContent = 'Passwords do not match.';
                errorDiv.classList.remove('d-none');
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Creating Account...';
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            const formData = new FormData(signupForm);
            formData.append('password_confirmation', confirmPassword);
            
            try {
                const response = await fetch('https://admin.droutlier.com/api/register', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success || response.ok) {
                    successDiv.textContent = 'Account created! Redirecting to login...';
                    successDiv.classList.remove('d-none');
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('signupModal')).hide();
                        new bootstrap.Modal(document.getElementById('loginModal')).show();
                    }, 1500);
                } else {
                    errorDiv.textContent = data.message || 'Registration failed. Please try again.';
                    errorDiv.classList.remove('d-none');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Sign Up';
            }
        });
    }
    
    // Forgot Password Form Submit
    if (forgotForm) {
        forgotForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('forgotBtn');
            const errorDiv = document.getElementById('forgot-error');
            const successDiv = document.getElementById('forgot-success');
            
            btn.disabled = true;
            btn.textContent = 'Sending...';
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            const formData = new FormData(forgotForm);
            
            try {
                const response = await fetch('https://admin.droutlier.com/api/forgot-password', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success || response.ok) {
                    successDiv.textContent = 'Reset link sent! Check your email.';
                    successDiv.classList.remove('d-none');
                    forgotForm.reset();
                } else {
                    errorDiv.textContent = data.message || 'Failed to send reset link.';
                    errorDiv.classList.remove('d-none');
                }
            } catch (error) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Send Reset Link';
            }
        });
    }
    
    // Google Login (placeholder - requires Google OAuth setup)
    const googleLoginBtn = document.getElementById('googleLoginBtn');
    const googleSignupBtn = document.getElementById('googleSignupBtn');
    
    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', function() {
            alert('Google OAuth login requires additional setup. Please contact admin or use email/password login.');
        });
    }
    
    if (googleSignupBtn) {
        googleSignupBtn.addEventListener('click', function() {
            alert('Google OAuth signup requires additional setup. Please contact admin or use email/password signup.');
        });
    }
});
</script>
