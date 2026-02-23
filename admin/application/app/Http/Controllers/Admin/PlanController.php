<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class PlanController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function index()
    {
        $pageTitle = 'All Plans';
        $plans = Plan::with('modules')->orderBy('sort_order')->paginate(getPaginate());
        return view('admin.plans.index', compact('pageTitle', 'plans'));
    }

    public function create()
    {
        $pageTitle = 'Create Plan';
        $modules = Module::active()->ordered()->get();
        return view('admin.plans.create', compact('pageTitle', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration_type' => 'required|in:days,months,years',
            'duration_value' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
            'sort_order' => 'nullable|integer',
        ]);

        // Create Razorpay Plan (optional - depending on if you want to use Razorpay subscriptions)
        $razorpayPlanId = null;
        try {
            $period = $request->duration_type === 'days' ? 'daily' :
                     ($request->duration_type === 'months' ? 'monthly' : 'yearly');

            $interval = $request->duration_value;

            $razorpayPlan = $this->razorpay->plan->create([
                'period' => $period,
                'interval' => $interval,
                'item' => [
                    'name' => $request->name,
                    'amount' => $request->price * 100, // Amount in paise
                    'currency' => 'INR',
                    'description' => $request->description ?? ''
                ]
            ]);

            $razorpayPlanId = $razorpayPlan->id;
        } catch (\Exception $e) {
            // Log the error but continue - Razorpay plan creation is optional
            \Log::error('Razorpay plan creation failed: ' . $e->getMessage());
        }

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->slug = $request->slug;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->discount_price = $request->discount_price;
        $plan->duration_type = $request->duration_type;
        $plan->duration_value = $request->duration_value;
        $plan->razorpay_plan_id = $razorpayPlanId;
        $plan->features = $request->features;
        $plan->sort_order = $request->sort_order ?? 0;
        $plan->is_active = $request->has('is_active') ? 1 : 0;
        $plan->is_featured = $request->has('is_featured') ? 1 : 0;
        $plan->save();

        // Attach modules to plan
        $plan->modules()->sync($request->modules);

        $notify[] = ['success', 'Plan created successfully'];
        return redirect()->route('admin.plans.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Plan';
        $plan = Plan::with('modules')->findOrFail($id);
        $modules = Module::active()->ordered()->get();
        return view('admin.plans.edit', compact('pageTitle', 'plan', 'modules'));
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration_type' => 'required|in:days,months,years',
            'duration_value' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
            'sort_order' => 'nullable|integer',
        ]);

        $plan->name = $request->name;
        $plan->slug = $request->slug;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->discount_price = $request->discount_price;
        $plan->duration_type = $request->duration_type;
        $plan->duration_value = $request->duration_value;
        $plan->features = $request->features;
        $plan->sort_order = $request->sort_order ?? 0;
        $plan->is_active = $request->has('is_active') ? 1 : 0;
        $plan->is_featured = $request->has('is_featured') ? 1 : 0;
        $plan->save();

        // Update modules
        $plan->modules()->sync($request->modules);

        $notify[] = ['success', 'Plan updated successfully'];
        return redirect()->route('admin.plans.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $plan = Plan::findOrFail($id);

        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            $notify[] = ['error', 'Cannot delete plan with active subscriptions'];
            return back()->withNotify($notify);
        }

        $plan->delete();

        $notify[] = ['success', 'Plan deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->is_active = $request->status;
        $plan->save();

        $notify[] = ['success', 'Plan status updated successfully'];
        return back()->withNotify($notify);
    }

    public function planData()
    {
        $plans = Plan::with('modules')->orderBy('sort_order')->get();
        return response()->json($plans);
    }
}
