<?php
/**
 * Subscription Page - Subscription Management
 * View current subscription status and history
 */

session_start();

// Include helpers
require_once __DIR__ . '/includes/api-helper.php';

// Check authentication
requireAuth();

$pageTitle = 'My Subscription - Dr. Outlier';
$user = getUser();
$token = getToken();

// Fetch subscription history
$loading = true;
$subscriptions = [];
$error = null;

$response = apiRequest('/subscription/history', 'GET', [], $token);

if (isset($response['error'])) {
    $error = $response['error'];
    $loading = false;
} elseif (isset($response['data']['subscriptions'])) {
    $subscriptions = $response['data']['subscriptions'];
    $loading = false;
} else {
    $loading = false;
}

// Get active subscription
$activeSubscription = null;
foreach ($subscriptions as $sub) {
    if (($sub['status'] ?? '') === 'active') {
        $activeSubscription = $sub;
        break;
    }
}

// Status badge helper
function getStatusBadge($status) {
    $badges = [
        'active' => ['color' => '#4CAF50', 'text' => 'Active'],
        'expired' => ['color' => '#FF5252', 'text' => 'Expired'],
        'pending' => ['color' => '#FFA500', 'text' => 'Pending'],
        'cancelled' => ['color' => '#9E9E9E', 'text' => 'Cancelled'],
    ];
    
    $badge = $badges[$status] ?? $badges['pending'];
    
    return '<span style="background: ' . $badge['color'] . '; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">' . $badge['text'] . '</span>';
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
    transition: all 0.3s;
}

.glass-card:hover {
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.stat-card {
    background: linear-gradient(135deg, #126E97 0%, #0d5070 100%);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
}

.subscription-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.subscription-card:hover {
    border-color: #126E97;
    transform: translateX(5px);
}

.loginBtn {
    background: linear-gradient(135deg, #126E97 0%, #0d5070 100%);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.loginBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(18, 110, 151, 0.4);
    color: white;
}
</style>

<!-- Main Content -->
<div class="main-wrapper" style="background: #1B1E27; min-height: 100vh; padding: 60px 0;">
    <div class="container">
        <!-- Page Header -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1 class="text-white mb-2" style="font-size: 32px; font-weight: 700;">
                    <i class="fas fa-crown me-3" style="color: #FFA500;"></i>
                    My Subscription
                </h1>
                <p style="color: rgba(255, 255, 255, 0.70); font-size: 16px; margin-bottom: 0;">
                    Manage your subscription, track your access, and view purchase history
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="/pricing" class="loginBtn">
                    <i class="fas fa-arrow-up me-2"></i>
                    Upgrade Plan
                </a>
            </div>
        </div>

        <?php if ($loading): ?>
        <!-- Loader -->
        <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <?php elseif ($error): ?>
        <!-- Error Message -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        
        <!-- Current Active Subscription -->
        <?php if ($activeSubscription): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="glass-card" style="background: linear-gradient(135deg, rgba(18, 110, 151, 0.2) 0%, rgba(13, 80, 112, 0.2) 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-3" style="font-weight: 700;">
                                <i class="fas fa-star me-2" style="color: #FFA500;"></i>
                                Current Plan: <?php echo htmlspecialchars($activeSubscription['plan_name'] ?? 'Premium'); ?>
                            </h3>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">
                                        <strong>Status:</strong> <?php echo getStatusBadge($activeSubscription['status'] ?? 'active'); ?>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">
                                        <strong>Amount:</strong> ₹<?php echo number_format($activeSubscription['amount'] ?? 0, 2); ?>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">
                                        <strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($activeSubscription['start_date'] ?? 'now')); ?>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">
                                        <strong>End Date:</strong> <?php echo date('M d, Y', strtotime($activeSubscription['end_date'] ?? 'now')); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mt-3 mt-md-0">
                            <i class="fas fa-check-circle" style="font-size: 64px; color: #4CAF50;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <i class="fas fa-calendar-check mb-2" style="font-size: 28px; color: #FFA500;"></i>
                    <h3 class="text-white mb-1" style="font-size: 24px; font-weight: 700;">
                        <?php echo count(array_filter($subscriptions, fn($s) => ($s['status'] ?? '') === 'active')); ?>
                    </h3>
                    <p style="color: rgba(255, 255, 255, 0.80); font-size: 14px; margin: 0;">
                        Active Subscriptions
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <i class="fas fa-history mb-2" style="font-size: 28px; color: #FFA500;"></i>
                    <h3 class="text-white mb-1" style="font-size: 24px; font-weight: 700;">
                        <?php echo count($subscriptions); ?>
                    </h3>
                    <p style="color: rgba(255, 255, 255, 0.80); font-size: 14px; margin: 0;">
                        Total Subscriptions
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <i class="fas fa-rupee-sign mb-2" style="font-size: 28px; color: #FFA500;"></i>
                    <h3 class="text-white mb-1" style="font-size: 24px; font-weight: 700;">
                        ₹<?php echo number_format(array_sum(array_column($subscriptions, 'amount')), 2); ?>
                    </h3>
                    <p style="color: rgba(255, 255, 255, 0.80); font-size: 14px; margin: 0;">
                        Total Spent
                    </p>
                </div>
            </div>
        </div>

        <!-- Subscription History -->
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <h5 class="text-white mb-4" style="font-weight: 600;">
                        <i class="fas fa-history me-2" style="color: #126E97;"></i>
                        Subscription History
                    </h5>
                    
                    <?php if (empty($subscriptions)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 48px; color: rgba(255, 255, 255, 0.3); margin-bottom: 20px;"></i>
                        <p style="color: rgba(255, 255, 255, 0.7); font-size: 16px;">
                            No subscription history found
                        </p>
                        <a href="/pricing" class="loginBtn mt-3">
                            <i class="fas fa-crown me-2"></i>
                            Subscribe Now
                        </a>
                    </div>
                    <?php else: ?>
                    
                    <?php foreach ($subscriptions as $subscription): ?>
                    <div class="subscription-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-2" style="font-weight: 600;">
                                    <?php echo htmlspecialchars($subscription['plan_name'] ?? 'Plan'); ?>
                                </h6>
                                <div class="row">
                                    <div class="col-sm-6 mb-1">
                                        <small style="color: rgba(255, 255, 255, 0.7);">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y', strtotime($subscription['start_date'] ?? 'now')); ?> - 
                                            <?php echo date('M d, Y', strtotime($subscription['end_date'] ?? 'now')); ?>
                                        </small>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <small style="color: rgba(255, 255, 255, 0.7);">
                                            <i class="fas fa-rupee-sign me-1"></i>
                                            ₹<?php echo number_format($subscription['amount'] ?? 0, 2); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php if (!empty($subscription['payment_id'])): ?>
                                <small style="color: rgba(255, 255, 255, 0.5);">
                                    Payment ID: <?php echo htmlspecialchars($subscription['payment_id']); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <?php echo getStatusBadge($subscription['status'] ?? 'pending'); ?>
                            </div>
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
