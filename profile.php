<?php
/**
 * Profile Page - User Profile Management
 * Edit profile, change password, update photo
 */

session_start();

// Include helpers
require_once __DIR__ . '/includes/api-helper.php';

// Check authentication
requireAuth();

$pageTitle = 'My Profile - Dr. Outlier';
$user = getUser();
$token = getToken();
$userId = $_SESSION['user_id'] ?? '';

// Handle form submissions
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            // Update profile
            $profileData = [
                'user_id' => $userId,
                'firstname' => $_POST['firstname'] ?? '',
                'lastname' => $_POST['lastname'] ?? '',
                'mobile' => $_POST['mobile'] ?? '',
                'country_code' => $_POST['country_code'] ?? '+91'
            ];
            
            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                // For now, we'll just send the user data without photo
                // In production, you'd upload to a server and get URL
            }
            
            $response = apiRequest('/user/update', 'POST', $profileData, $token);
            
            if (isset($response['status']) && $response['status'] === 'success') {
                $successMsg = 'Profile updated successfully!';
                // Update session
                $_SESSION['user']['name'] = $profileData['firstname'] . ' ' . $profileData['lastname'];
            } else {
                $errorMsg = $response['message'] ?? 'Failed to update profile';
            }
        } elseif ($_POST['action'] === 'change_password') {
            // Change password
            $passwordData = [
                'user_id' => $userId,
                'old_password' => $_POST['old_password'] ?? '',
                'new_password' => $_POST['new_password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? ''
            ];
            
            if ($passwordData['new_password'] !== $passwordData['confirm_password']) {
                $errorMsg = 'New passwords do not match';
            } else {
                $response = apiRequest('/user/change-password', 'POST', $passwordData, $token);
                
                if (isset($response['status']) && $response['status'] === 'success') {
                    $successMsg = 'Password changed successfully!';
                } else {
                    $errorMsg = $response['message'] ?? 'Failed to change password';
                }
            }
        }
    }
}

// Fetch user data
$userData = [];
$countries = [];

$userResponse = apiRequest('/user-data', 'POST', ['user_id' => $userId], $token);
if (isset($userResponse['data']['list'])) {
    $userData = $userResponse['data']['list'];
}

// Fetch countries for dropdown
$countriesResponse = apiRequest('/get-countries', 'GET', [], $token);
if (isset($countriesResponse['data'])) {
    $countries = $countriesResponse['data'];
}

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

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: white;
    border-radius: 8px;
    padding: 12px 16px;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: #126E97;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(18, 110, 151, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.form-label {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    margin-bottom: 8px;
}

.profile-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid #126E97;
    object-fit: cover;
    margin-bottom: 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #126E97 0%, #0d5070 100%);
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(18, 110, 151, 0.4);
}

.form-select option {
    background: #1B1E27;
    color: white;
}
</style>

<!-- Main Content -->
<div class="main-wrapper" style="background: #1B1E27; min-height: 100vh; padding: 60px 0;">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-white mb-2" style="font-size: 32px; font-weight: 700;">
                    <i class="fas fa-user-circle me-3" style="color: #126E97;"></i>
                    My Profile
                </h1>
                <p style="color: rgba(255, 255, 255, 0.70); font-size: 16px;">
                    Manage your account settings and preferences
                </p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($successMsg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($errorMsg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column - Profile Info -->
            <div class="col-lg-4 mb-4">
                <div class="glass-card text-center">
                    <img 
                        src="<?php echo !empty($userData['image']) ? 'https://admin.droutlier.com/' . htmlspecialchars($userData['image']) : '/public/images/avatar.png'; ?>" 
                        alt="Profile Photo" 
                        class="profile-image"
                    />
                    <h4 class="text-white mb-2">
                        <?php echo htmlspecialchars(($userData['firstname'] ?? '') . ' ' . ($userData['lastname'] ?? '')); ?>
                    </h4>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 20px;">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($userData['email'] ?? ''); ?>
                    </p>
                    <?php if (!empty($userData['mobile'])): ?>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 0;">
                        <i class="fas fa-phone me-2"></i>
                        <?php echo htmlspecialchars(($userData['country_code'] ?? '') . ' ' . $userData['mobile']); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Edit Forms -->
            <div class="col-lg-8">
                <!-- Edit Profile Form -->
                <div class="glass-card mb-4">
                    <h5 class="text-white mb-4" style="font-weight: 600;">
                        <i class="fas fa-edit me-2" style="color: #126E97;"></i>
                        Edit Profile
                    </h5>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input 
                                    type="text" 
                                    name="firstname" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($userData['firstname'] ?? ''); ?>"
                                    required
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input 
                                    type="text" 
                                    name="lastname" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($userData['lastname'] ?? ''); ?>"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country Code</label>
                                <select name="country_code" class="form-select">
                                    <?php 
                                    $currentCode = $userData['country_code'] ?? '+91';
                                    foreach ($countries as $country): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($country['code'] ?? ''); ?>" 
                                            <?php echo ($country['code'] ?? '') === $currentCode ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['code'] ?? ''); ?> - <?php echo htmlspecialchars($country['name'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Mobile Number</label>
                                <input 
                                    type="tel" 
                                    name="mobile" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($userData['mobile'] ?? ''); ?>"
                                    placeholder="Enter mobile number"
                                />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Photo</label>
                            <input 
                                type="file" 
                                name="photo" 
                                class="form-control" 
                                accept="image/*"
                            />
                            <small style="color: rgba(255, 255, 255, 0.6);">
                                Upload a new profile photo (JPG, PNG)
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="glass-card">
                    <h5 class="text-white mb-4" style="font-weight: 600;">
                        <i class="fas fa-lock me-2" style="color: #126E97;"></i>
                        Change Password
                    </h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input 
                                type="password" 
                                name="old_password" 
                                class="form-control" 
                                placeholder="Enter current password"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input 
                                type="password" 
                                name="new_password" 
                                class="form-control" 
                                placeholder="Enter new password"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input 
                                type="password" 
                                name="confirm_password" 
                                class="form-control" 
                                placeholder="Re-enter new password"
                                required
                            />
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
