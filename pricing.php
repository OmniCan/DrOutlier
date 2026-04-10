<?php
session_start();
require_once 'includes/api-helper.php';

// Check if user is logged in
$isAuthenticated = isset($_SESSION['user_token']);
$plans = [];
$error = null;

if ($isAuthenticated) {
    // Fetch subscription plans
    $response = apiRequest('/subscription/plans', 'GET');
    
    if ($response && isset($response['status']) && $response['status'] === 'success') {
        $plans = $response['data']['plans'] ?? [];
    } else {
        $error = 'Failed to load plans';
    }
}

// Helper function to determine column class based on number of plans
function getColumnClass($totalPlans, $index) {
    if ($totalPlans == 1) {
        return 'col-12';
    } elseif ($totalPlans == 2) {
        return 'col-lg-6 col-md-6';
    } elseif ($totalPlans == 3) {
        return 'col-lg-4 col-md-6';
    } elseif ($totalPlans == 4) {
        return 'col-lg-3 col-md-6';
    } elseif ($totalPlans == 5) {
        return $index < 4 ? 'col-lg-3 col-md-6' : 'col-12';
    } elseif ($totalPlans == 6) {
        return 'col-lg-4 col-md-6';
    } elseif ($totalPlans == 7) {
        return $index < 4 ? 'col-lg-3 col-md-6' : 'col-lg-4 col-md-6';
    } elseif ($totalPlans == 8) {
        return 'col-lg-3 col-md-6';
    } else {
        return 'col-lg-4 col-md-6';
    }
}

// Check if we need a new row (for 5 items layout)
function shouldStartNewRow($totalPlans, $index) {
    return $totalPlans == 5 && $index == 4;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - DrOutlier</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            background: #1B1E27;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-wrapper {
            min-height: 80vh;
            padding: 40px 0;
        }

        .pricing-card {
            border-radius: 15px;
            padding: 30px;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(18, 110, 151, 0.3);
        }

        .featured-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #FFA500;
            color: #fff;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        .subscribe-btn {
            background: #126E97;
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: auto;
        }

        .subscribe-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(18, 110, 151, 0.4);
        }

        .subscribe-btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .subscribe-btn.featured {
            background: #FFA500;
        }

        .why-choose {
            background: #282D41;
            border-radius: 10px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 50px;
        }

        .loader {
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid #126E97;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 100px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: #282D41;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left: 4px solid #4CAF50;
        }

        .toast.error {
            border-left: 4px solid #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="main-wrapper">
        <section>
            <div class="container">
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <h1 class="text-white mb-3">Choose Your Plan</h1>
                    <p style="color: rgba(255, 255, 255, 0. 70); font-size: 18px;">
                        Select the perfect plan for your radiology exam preparation
                    </p>
                </div>

                <?php if (!$isAuthenticated): ?>
                    <!-- Not Authenticated -->
                    <div class="text-center" style="min-height: 400px; padding-top: 100px;">
                        <h3 class="text-white mb-4">Please Login to View Plans</h3>
                        <button onclick="showLoginModal()" class="subscribe-btn" style="max-width: 200px; margin: 0 auto;">
                            Login Now
                        </button>
                    </div>

                <?php elseif ($error): ?>
                    <!-- Error State -->
                    <div class="text-center" style="min-height: 400px; padding-top: 100px;">
                        <p class="text-danger mb-4"><?php echo htmlspecialchars($error); ?></p>
                        <button onclick="location.reload()" class="subscribe-btn" style="max-width: 200px; margin: 0 auto;">
                            Retry
                        </button>
                    </div>

                <?php elseif (empty($plans)): ?>
                    <!-- No Plans Available -->
                    <div class="text-center" style="min-height: 400px; padding-top: 100px;">
                        <h3 class="text-white">No Plans Available</h3>
                        <p style="color: rgba(255, 255, 255, 0.60);">Please check back later</p>
                    </div>

                <?php else: ?>
                    <!-- Pricing Cards -->
                    <div class="row justify-content-center" style="row-gap: 30px;">
                        <?php foreach ($plans as $index => $plan): ?>
                            <?php if (shouldStartNewRow(count($plans), $index)): ?>
                                <div class="w-100" style="margin-bottom: 30px;"></div>
                            <?php endif; ?>

                            <div class="<?php echo getColumnClass(count($plans), $index); ?> mb-4">
                                <div class="pricing-card" style="
                                    background: <?php echo !empty($plan['is_featured']) ? 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)' : '#282D41'; ?>;
                                    border: <?php echo !empty($plan['is_featured']) ? '2px solid #126E97' : '1px solid rgba(255, 255, 255, 0.1)'; ?>;
                                    transform: <?php echo !empty($plan['is_featured']) ? 'scale(1.05)' : 'scale(1)'; ?>;
                                ">
                                    <?php if (!empty($plan['is_featured'])): ?>
                                        <div class="featured-badge">MOST POPULAR</div>
                                    <?php endif; ?>

                                    <div class="text-center mb-4">
                                        <h3 class="text-white mb-2" style="font-size: 24px; font-weight: 700;">
                                            <?php echo htmlspecialchars($plan['name']); ?>
                                        </h3>
                                        <p style="color: rgba(255, 255, 255, 0.70); font-size: 14px; min-height: 40px;">
                                            <?php echo htmlspecialchars($plan['description'] ?? ''); ?>
                                        </p>
                                    </div>

                                    <div class="text-center mb-4">
                                        <div class="d-flex align-items-baseline justify-content-center">
                                            <span class="text-white" style="font-size: 42px; font-weight: 700;">
                                                ₹<?php echo number_format($plan['effective_price']); ?>
                                            </span>
                                            <span style="color: rgba(255, 255, 255, 0.60); margin-left: 10px;">
                                                / <?php echo htmlspecialchars($plan['duration_text']); ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($plan['discount_price']) && $plan['discount_price'] > 0): ?>
                                            <p style="color: rgba(255, 255, 255, 0.50); text-decoration: line-through; font-size: 16px;">
                                                ₹<?php echo number_format($plan['price']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-4">
                                        <h5 class="text-white mb-3" style="font-size: 16px; font-weight: 600;">
                                            Includes Access To:
                                        </h5>
                                        <ul class="list-unstyled">
                                            <?php foreach ($plan['modules'] as $module): ?>
                                                <li class="mb-2 d-flex align-items-center">
                                                    <i class="<?php echo htmlspecialchars($module['icon'] ?? 'fas fa-check-circle'); ?> me-2" style="color: #FFA500;"></i>
                                                    <span style="color: rgba(255, 255, 255, 0.80);">
                                                        <?php echo htmlspecialchars($module['name']); ?>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <?php if (!empty($plan['features']) && count($plan['features']) > 0): ?>
                                        <div class="mb-4">
                                            <h5 class="text-white mb-3" style="font-size: 16px; font-weight: 600;">
                                                Features:
                                            </h5>
                                            <ul class="list-unstyled">
                                                <?php foreach ($plan['features'] as $feature): ?>
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <i class="fas fa-check me-2 mt-1" style="color: #4CAF50;"></i>
                                                        <span style="color: rgba(255, 255, 255, 0.70); font-size: 14px;">
                                                            <?php echo htmlspecialchars($feature); ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <button 
                                        onclick="handlePurchase(<?php echo htmlspecialchars(json_encode($plan)); ?>)"
                                        class="subscribe-btn <?php echo !empty($plan['is_featured']) ? 'featured' : ''; ?>"
                                        id="subscribe-btn-<?php echo $plan['id']; ?>"
                                    >
                                        Subscribe Now
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Why Choose DrOutlier Section -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="why-choose">
                                <h4 class="text-white mb-4 text-center">Why Choose DrOutlier?</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3 text-center">
                                        <i class="fas fa-graduation-cap mb-2" style="font-size: 32px; color: #126E97;"></i>
                                        <h6 class="text-white">Expert Content</h6>
                                        <p style="color: rgba(255, 255, 255, 0.60); font-size: 14px;">
                                            Curated by radiology experts
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-3 text-center">
                                        <i class="fas fa-mobile-alt mb-2" style="font-size: 32px; color: #126E97;"></i>
                                        <h6 class="text-white">Learn Anywhere</h6>
                                        <p style="color: rgba(255, 255, 255, 0.60); font-size: 14px;">
                                            Access on any device, anytime
                                        </p>
                                    </div>
                                    <div class="col-md-4 mb-3 text-center">
                                        <i class="fas fa-trophy mb-2" style="font-size: 32px; color: #126E97;"></i>
                                        <h6 class="text-white">Exam Success</h6>
                                        <p style="color: rgba(255, 255, 255, 0.60); font-size: 14px;">
                                            Proven track record of success
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Login Modals -->
    <?php include 'includes/login-modals.php'; ?>

    <!-- Razorpay SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        const API_BASE_URL = 'https://admin.droutlier.com/api';
        let processing = false;

        function showLoginModal() {
            const myModal = new bootstrap.Modal(document.getElementById('myModal'));
            myModal.show();
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function disableButton(planId, text) {
            const btn = document.getElementById(`subscribe-btn-${planId}`);
            if (btn) {
                btn.disabled = true;
                btn.textContent = text;
            }
        }

        function enableButton(planId) {
            const btn = document.getElementById(`subscribe-btn-${planId}`);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Subscribe Now';
            }
        }

        async function handlePurchase(plan) {
            if (processing) return;

            <?php if (!$isAuthenticated): ?>
                showToast('Please login to purchase a plan', 'error');
                setTimeout(showLoginModal, 500);
                return;
            <?php endif; ?>

            processing = true;
            disableButton(plan.id, 'Processing...');

            try {
                // Create order
                const orderResponse = await fetch(`${API_BASE_URL}/subscription/create-order`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer <?php echo $_SESSION['user_token'] ?? ''; ?>',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ plan_id: plan.id }),
                });

                const orderData = await orderResponse.json();

                if (orderData.status !== 'success') {
                    throw new Error(orderData.message?.error?.[0] || 'Failed to create order');
                }

                // Initialize Razorpay
                const options = {
                    key: orderData.data.razorpay_key,
                    amount: orderData.data.amount,
                    currency: orderData.data.currency,
                    name: 'DrOutlier',
                    description: `${plan.name} Subscription`,
                    order_id: orderData.data.order_id,
                    handler: async function (response) {
                        try {
                            // Verify payment
                            const verifyResponse = await fetch(`${API_BASE_URL}/subscription/verify-payment`, {
                                method: 'POST',
                                headers: {
                                    'Authorization': 'Bearer <?php echo $_SESSION['user_token'] ?? ''; ?>',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature,
                                    subscription_id: orderData.data.subscription_id,
                                }),
                            });

                            const verifyData = await verifyResponse.json();

                            if (verifyData.status === 'success') {
                                showToast(`Successfully subscribed to ${plan.name}!`, 'success');
                                setTimeout(() => {
                                    window.location.href = '/subscription.php';
                                }, 2000);
                            } else {
                                throw new Error(verifyData.message?.error?.[0] || 'Payment verification failed');
                            }
                        } catch (error) {
                            processing = false;
                            enableButton(plan.id);
                            showToast(`Payment verification failed: ${error.message}`, 'error');
                        }
                    },
                    theme: {
                        color: '#126E97',
                    },
                    modal: {
                        ondismiss: function() {
                            processing = false;
                            enableButton(plan.id);
                            showToast('Payment cancelled', 'error');
                        }
                    }
                };

                const razorpay = new Razorpay(options);
                razorpay.open();
            } catch (error) {
                processing = false;
                enableButton(plan.id);
                showToast(`Failed to initiate payment: ${error.message}`, 'error');
            }
        }
    </script>
</body>
</html>
