<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'remark' => 'unauthenticated',
                'status' => 'error',
                'message' => ['error' => ['Please login to continue']],
            ], 401);
        }

        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'remark' => 'no_subscription',
                'status' => 'error',
                'message' => ['error' => ['No active subscription found. Please subscribe to access this content.']],
            ], 403);
        }

        return $next($request);
    }
}
