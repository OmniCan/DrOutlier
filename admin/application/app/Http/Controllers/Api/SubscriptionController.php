<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Module;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        try {
            $key = config('razorpay.key');
            $secret = config('razorpay.secret');

            Log::info('Razorpay Config Check', [
                'key_present' => !empty($key),
                'secret_present' => !empty($secret),
                'key_value' => $key ? substr($key, 0, 10) . '...' : 'NULL',
            ]);

            if (!$key || !$secret) {
                Log::error('Razorpay credentials missing', [
                    'key' => $key ?: 'NOT SET',
                    'secret' => $secret ? 'SET' : 'NOT SET',
                ]);
                throw new \Exception('Razorpay credentials not configured. Please check .env file.');
            }

            $this->razorpay = new Api($key, $secret);
            Log::info('Razorpay API initialized successfully');
        } catch (\Exception $e) {
            Log::error('Razorpay initialization failed in constructor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get all active plans with modules
     */
    public function getPlans()
    {
        $plans = Plan::with('modules')
            ->active()
            ->ordered()
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'discount_price' => $plan->discount_price,
                    'effective_price' => $plan->effective_price,
                    'duration_type' => $plan->duration_type,
                    'duration_value' => $plan->duration_value,
                    'duration_text' => $plan->duration_text,
                    'is_featured' => $plan->is_featured,
                    'features' => $plan->features,
                    'modules' => $plan->modules->map(function ($module) {
                        return [
                            'id' => $module->id,
                            'name' => $module->display_name,
                            'slug' => $module->slug,
                            'icon' => $module->icon,
                            'description' => $module->description,
                        ];
                    }),
                ];
            });

        return response()->json([
            'remark' => 'plans_list',
            'status' => 'success',
            'message' => ['success' => ['Plans retrieved successfully']],
            'data' => [
                'plans' => $plans
            ]
        ]);
    }

    /**
     * Create Razorpay order for plan purchase
     */
    public function createOrder(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
            ]);

            $user = auth()->user();
            $plan = Plan::findOrFail($request->plan_id);

            if (!$plan->is_active) {
                return response()->json([
                    'remark' => 'plan_inactive',
                    'status' => 'error',
                    'message' => ['error' => ['This plan is not available']],
                ], 400);
            }

            Log::info('Creating Razorpay order', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'amount' => $plan->effective_price,
            ]);

            try {
                $amount = $plan->effective_price * 100; // Amount in paise

                $orderData = [
                    'receipt' => 'order_' . time() . '_' . $user->id,
                    'amount' => $amount,
                    'currency' => 'INR',
                    'notes' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'plan_name' => $plan->name,
                    ]
                ];

                $razorpayOrder = $this->razorpay->order->create($orderData);

                // Create pending subscription
                $subscription = new UserSubscription();
                $subscription->user_id = $user->id;
                $subscription->plan_id = $plan->id;
                $subscription->razorpay_order_id = $razorpayOrder['id'];
                $subscription->amount_paid = $plan->effective_price;
                $subscription->status = 'pending';
                $subscription->save();

                return response()->json([
                    'remark' => 'order_created',
                    'status' => 'success',
                    'message' => ['success' => ['Order created successfully']],
                    'data' => [
                        'order_id' => $razorpayOrder['id'],
                        'amount' => $amount,
                        'currency' => 'INR',
                        'subscription_id' => $subscription->id,
                        'razorpay_key' => config('razorpay.key'),
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Razorpay Order Creation Failed', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'razorpay_key' => config('razorpay.key') ? 'SET' : 'NOT SET',
                    'razorpay_secret' => config('razorpay.secret') ? 'SET' : 'NOT SET',
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'remark' => 'order_creation_failed',
                    'status' => 'error',
                    'message' => ['error' => [$e->getMessage()]],
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Create Order Request Failed', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'remark' => 'request_failed',
                'status' => 'error',
                'message' => ['error' => ['An unexpected error occurred: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Verify payment and activate subscription
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'subscription_id' => 'required|exists:user_subscriptions,id',
        ]);

        $subscription = UserSubscription::findOrFail($request->subscription_id);

        if ($subscription->user_id !== auth()->id()) {
            return response()->json([
                'remark' => 'unauthorized',
                'status' => 'error',
                'message' => ['error' => ['Unauthorized access']],
            ], 403);
        }

        try {
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Fetch payment details
            $payment = $this->razorpay->payment->fetch($request->razorpay_payment_id);

            if ($payment->status === 'captured' || $payment->status === 'authorized') {
                // Calculate expiry date
                $plan = $subscription->plan;
                $startDate = now();

                switch ($plan->duration_type) {
                    case 'days':
                        $expiryDate = $startDate->copy()->addDays($plan->duration_value);
                        break;
                    case 'months':
                        $expiryDate = $startDate->copy()->addMonths($plan->duration_value);
                        break;
                    case 'years':
                        $expiryDate = $startDate->copy()->addYears($plan->duration_value);
                        break;
                    default:
                        $expiryDate = $startDate->copy()->addMonths(1);
                }

                // Update subscription
                $subscription->razorpay_payment_id = $request->razorpay_payment_id;
                $subscription->razorpay_subscription_id = $request->razorpay_signature;
                $subscription->status = 'active';
                $subscription->started_at = $startDate;
                $subscription->expires_at = $expiryDate;
                $subscription->payment_details = [
                    'payment_id' => $payment->id,
                    'method' => $payment->method ?? null,
                    'email' => $payment->email ?? null,
                    'contact' => $payment->contact ?? null,
                ];
                $subscription->save();

                return response()->json([
                    'remark' => 'payment_verified',
                    'status' => 'success',
                    'message' => ['success' => ['Payment verified and subscription activated']],
                    'data' => [
                        'subscription' => [
                            'id' => $subscription->id,
                            'plan' => $plan->name,
                            'started_at' => $subscription->started_at->format('Y-m-d H:i:s'),
                            'expires_at' => $subscription->expires_at->format('Y-m-d H:i:s'),
                            'status' => $subscription->status,
                        ]
                    ]
                ]);
            } else {
                return response()->json([
                    'remark' => 'payment_failed',
                    'status' => 'error',
                    'message' => ['error' => ['Payment not successful']],
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'remark' => 'verification_failed',
                'status' => 'error',
                'message' => ['error' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Get user's active subscription
     */
    public function getMySubscription()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return response()->json([
                'remark' => 'no_active_subscription',
                'status' => 'success',
                'message' => ['success' => ['No active subscription found']],
                'data' => [
                    'subscription' => null,
                    'has_subscription' => false,
                ]
            ]);
        }

        return response()->json([
            'remark' => 'subscription_details',
            'status' => 'success',
            'message' => ['success' => ['Subscription details retrieved']],
            'data' => [
                'subscription' => [
                    'id' => $subscription->id,
                    'plan' => [
                        'id' => $subscription->plan->id,
                        'name' => $subscription->plan->name,
                        'description' => $subscription->plan->description,
                    ],
                    'started_at' => $subscription->started_at->format('Y-m-d H:i:s'),
                    'expires_at' => $subscription->expires_at->format('Y-m-d H:i:s'),
                    'days_remaining' => $subscription->days_remaining,
                    'status' => $subscription->status,
                    'modules' => $subscription->plan->modules->map(function ($module) {
                        return [
                            'id' => $module->id,
                            'name' => $module->display_name,
                            'slug' => $module->slug,
                            'frontend_url' => $module->frontend_url,
                            'icon' => $module->icon,
                        ];
                    }),
                ],
                'has_subscription' => true,
            ]
        ]);
    }

    /**
     * Get subscription history
     */
    public function getSubscriptionHistory()
    {
        $user = auth()->user();
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan->name,
                    'amount_paid' => $subscription->amount_paid,
                    'status' => $subscription->status,
                    'started_at' => $subscription->started_at ? $subscription->started_at->format('Y-m-d H:i:s') : null,
                    'expires_at' => $subscription->expires_at ? $subscription->expires_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $subscription->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'remark' => 'subscription_history',
            'status' => 'success',
            'message' => ['success' => ['Subscription history retrieved']],
            'data' => [
                'subscriptions' => $subscriptions
            ]
        ]);
    }

    /**
     * Check if user has access to a specific module
     */
    public function checkModuleAccess(Request $request)
    {
        $request->validate([
            'module_slug' => 'required|string',
        ]);

        $user = auth()->user();
        $hasAccess = $user->hasAccessToModule($request->module_slug);

        $module = Module::where('slug', $request->module_slug)->first();

        return response()->json([
            'remark' => 'module_access_check',
            'status' => 'success',
            'message' => ['success' => ['Access check completed']],
            'data' => [
                'has_access' => $hasAccess,
                'module' => $module ? [
                    'name' => $module->display_name,
                    'slug' => $module->slug,
                ] : null,
                'subscription_required' => !$hasAccess,
            ]
        ]);
    }

    /**
     * Get accessible modules for user
     */
    public function getAccessibleModules()
    {
        $user = auth()->user();
        $modules = $user->getAccessibleModules();

        return response()->json([
            'remark' => 'accessible_modules',
            'status' => 'success',
            'message' => ['success' => ['Accessible modules retrieved']],
            'data' => [
                'modules' => $modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->display_name,
                        'slug' => $module->slug,
                        'frontend_url' => $module->frontend_url,
                        'icon' => $module->icon,
                        'description' => $module->description,
                    ];
                }),
                'has_subscription' => $modules->isNotEmpty(),
            ]
        ]);
    }
}
