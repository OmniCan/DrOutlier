<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Subscriptions';
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->latest()
            ->paginate(getPaginate());

        $activeCount = UserSubscription::where('status', 'active')->count();
        $expiredCount = UserSubscription::where('status', 'expired')->count();
        $cancelledCount = UserSubscription::where('status', 'cancelled')->count();
        $totalRevenue = UserSubscription::where('status', 'active')->sum('amount_paid');

        return view('admin.subscriptions.index', compact('pageTitle', 'subscriptions', 'activeCount', 'expiredCount', 'cancelledCount', 'totalRevenue'));
    }

    public function active()
    {
        $pageTitle = 'Active Subscriptions';
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->active()
            ->latest()
            ->paginate(getPaginate());

        $activeCount = UserSubscription::where('status', 'active')->count();
        $expiredCount = UserSubscription::where('status', 'expired')->count();
        $cancelledCount = UserSubscription::where('status', 'cancelled')->count();
        $totalRevenue = UserSubscription::where('status', 'active')->sum('amount_paid');

        return view('admin.subscriptions.index', compact('pageTitle', 'subscriptions', 'activeCount', 'expiredCount', 'cancelledCount', 'totalRevenue'));
    }

    public function expired()
    {
        $pageTitle = 'Expired Subscriptions';
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->expired()
            ->latest()
            ->paginate(getPaginate());

        $activeCount = UserSubscription::where('status', 'active')->count();
        $expiredCount = UserSubscription::where('status', 'expired')->count();
        $cancelledCount = UserSubscription::where('status', 'cancelled')->count();
        $totalRevenue = UserSubscription::where('status', 'active')->sum('amount_paid');

        return view('admin.subscriptions.index', compact('pageTitle', 'subscriptions', 'activeCount', 'expiredCount', 'cancelledCount', 'totalRevenue'));
    }

    public function cancelled()
    {
        $pageTitle = 'Cancelled Subscriptions';
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->cancelled()
            ->latest()
            ->paginate(getPaginate());

        $activeCount = UserSubscription::where('status', 'active')->count();
        $expiredCount = UserSubscription::where('status', 'expired')->count();
        $cancelledCount = UserSubscription::where('status', 'cancelled')->count();
        $totalRevenue = UserSubscription::where('status', 'active')->sum('amount_paid');

        return view('admin.subscriptions.index', compact('pageTitle', 'subscriptions', 'activeCount', 'expiredCount', 'cancelledCount', 'totalRevenue'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Subscriptions';
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(getPaginate());

        return view('admin.subscriptions.index', compact('pageTitle', 'subscriptions'));
    }

    public function detail($id)
    {
        $pageTitle = 'Subscription Details';
        $subscription = UserSubscription::with(['user', 'plan.modules'])->findOrFail($id);

        return view('admin.subscriptions.detail', compact('pageTitle', 'subscription'));
    }

    public function create()
    {
        $pageTitle = 'Create Manual Subscription';
        $users = User::active()->orderBy('username')->get();
        $plans = Plan::active()->ordered()->get();

        return view('admin.subscriptions.create', compact('pageTitle', 'users', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'amount_paid' => 'required|numeric|min:0',
            'started_at' => 'required|date',
            'expires_at' => 'required|date|after:started_at',
        ]);

        $subscription = new UserSubscription();
        $subscription->user_id = $request->user_id;
        $subscription->plan_id = $request->plan_id;
        $subscription->amount_paid = $request->amount_paid;
        $subscription->started_at = $request->started_at;
        $subscription->expires_at = $request->expires_at;
        $subscription->status = 'active';
        $subscription->save();

        $notify[] = ['success', 'Subscription created successfully'];
        return redirect()->route('admin.subscriptions.index')->withNotify($notify);
    }

    public function cancel($id)
    {
        $subscription = UserSubscription::findOrFail($id);

        if ($subscription->status !== 'active') {
            $notify[] = ['error', 'Only active subscriptions can be cancelled'];
            return back()->withNotify($notify);
        }

        $subscription->cancel();

        $notify[] = ['success', 'Subscription cancelled successfully'];
        return back()->withNotify($notify);
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $subscription = UserSubscription::findOrFail($id);

        if (!$subscription->isActive()) {
            $notify[] = ['error', 'Only active subscriptions can be extended'];
            return back()->withNotify($notify);
        }

        $subscription->expires_at = Carbon::parse($subscription->expires_at)->addDays($request->days);
        $subscription->save();

        $notify[] = ['success', 'Subscription extended by ' . $request->days . ' days'];
        return back()->withNotify($notify);
    }

    public function subscriptionData()
    {
        $subscriptions = UserSubscription::with(['user', 'plan'])->latest()->get();
        return response()->json($subscriptions);
    }
}
