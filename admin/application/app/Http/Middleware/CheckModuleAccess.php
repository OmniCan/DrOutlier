<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Module;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $moduleSlug
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $moduleSlug)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'remark' => 'unauthenticated',
                'status' => 'error',
                'message' => ['error' => ['Please login to continue']],
            ], 401);
        }

        // Check if module exists
        $module = Module::where('slug', $moduleSlug)->first();

        if (!$module) {
            return response()->json([
                'remark' => 'module_not_found',
                'status' => 'error',
                'message' => ['error' => ['Module not found']],
            ], 404);
        }

        // TEMPORARY: Allow access if no active plans exist in the system (grace period)
        // This allows existing users to continue using the platform while you set up plans
        $activePlansExist = \App\Models\Plan::where('is_active', 1)->exists();

        if (!$activePlansExist) {
            // No active plans configured yet, allow free access
            return $next($request);
        }

        // Check if user has access to this module
        if (!$user->hasAccessToModule($moduleSlug)) {
            return response()->json([
                'remark' => 'access_denied',
                'status' => 'error',
                'message' => ['error' => ['You do not have access to this module. Please subscribe to a plan that includes this module.']],
                'data' => [
                    'module' => [
                        'name' => $module->display_name,
                        'slug' => $module->slug,
                    ],
                    'required_subscription' => true,
                ]
            ], 403);
        }

        return $next($request);
    }
}
