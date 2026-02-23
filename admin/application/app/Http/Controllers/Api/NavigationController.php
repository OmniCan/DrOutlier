<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;

class NavigationController extends Controller
{
    public function getNavigation()
    {
        $items = NavigationItem::getNavbarItems();

        // Filter items based on visibility type and user access
        $user = auth()->user();

        $filteredItems = $items->filter(function($item) use ($user) {
            // Check visibility type
            switch ($item->visibility_type ?? 'public') {
                case 'public':
                    // Show to everyone (no restrictions)
                    return true;

                case 'subscription':
                    // Show only to users with subscription access
                    if (!$user) {
                        return false; // Not logged in, can't check subscription
                    }
                    if ($item->module_id) {
                        return $user->hasAccessToModule($item->module->slug);
                    }
                    return true; // No module linked, show to all logged in users

                case 'auth':
                    // Show only to logged in users (regardless of subscription)
                    return $user !== null;

                default:
                    // Default to public
                    return true;
            }
        })->values();

        return response()->json([
            'remark' => 'navigation_items',
            'status' => 'success',
            'message' => ['success' => ['Navigation items retrieved successfully']],
            'data' => [
                'navigation' => $filteredItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'url' => $item->url,
                        'icon' => $item->icon,
                        'type' => $item->type,
                        'visibility_type' => $item->visibility_type ?? 'public',
                        'module' => $item->module ? [
                            'id' => $item->module->id,
                            'name' => $item->module->display_name,
                            'slug' => $item->module->slug,
                        ] : null,
                    ];
                })
            ]
        ]);
    }

    public function getPublicNavigation()
    {
        $items = NavigationItem::getNavbarItems()
            ->where('requires_auth', false);

        return response()->json([
            'remark' => 'public_navigation',
            'status' => 'success',
            'message' => ['success' => ['Public navigation items retrieved successfully']],
            'data' => [
                'navigation' => $items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'url' => $item->url,
                        'icon' => $item->icon,
                        'type' => $item->type,
                    ];
                })
            ]
        ]);
    }
}
