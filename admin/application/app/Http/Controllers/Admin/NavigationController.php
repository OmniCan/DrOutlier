<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use App\Models\Module;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        $pageTitle = 'Navigation Manager';
        $navigationItems = NavigationItem::with('module')->orderBy('sort_order')->paginate(getPaginate());
        return view('admin.navigation.index', compact('pageTitle', 'navigationItems'));
    }

    public function create()
    {
        $pageTitle = 'Add Navigation Item';
        $modules = Module::active()->ordered()->get();
        return view('admin.navigation.create', compact('pageTitle', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'module_id' => 'nullable|exists:modules,id',
            'sort_order' => 'nullable|integer',
            'type' => 'required|in:module,custom,external',
            'visibility_type' => 'required|in:public,subscription,auth',
        ]);

        $navigationItem = new NavigationItem();
        $navigationItem->title = $request->title;
        $navigationItem->url = $request->url;
        $navigationItem->icon = $request->icon;
        $navigationItem->module_id = $request->module_id;
        $navigationItem->sort_order = $request->sort_order ?? 0;
        $navigationItem->is_active = $request->has('is_active') ? 1 : 0;
        $navigationItem->show_in_navbar = $request->has('show_in_navbar') ? 1 : 0;
        $navigationItem->requires_auth = $request->has('requires_auth') ? 1 : 0;
        $navigationItem->type = $request->type;
        $navigationItem->visibility_type = $request->visibility_type;
        $navigationItem->save();

        $notify[] = ['success', 'Navigation item created successfully'];
        return redirect()->route('admin.navigation.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Navigation Item';
        $navigationItem = NavigationItem::findOrFail($id);
        $modules = Module::active()->ordered()->get();
        return view('admin.navigation.edit', compact('pageTitle', 'navigationItem', 'modules'));
    }

    public function update(Request $request, $id)
    {
        $navigationItem = NavigationItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'module_id' => 'nullable|exists:modules,id',
            'sort_order' => 'nullable|integer',
            'type' => 'required|in:module,custom,external',
            'visibility_type' => 'required|in:public,subscription,auth',
        ]);

        $navigationItem->title = $request->title;
        $navigationItem->url = $request->url;
        $navigationItem->icon = $request->icon;
        $navigationItem->module_id = $request->module_id;
        $navigationItem->sort_order = $request->sort_order ?? 0;
        $navigationItem->is_active = $request->has('is_active') ? 1 : 0;
        $navigationItem->show_in_navbar = $request->has('show_in_navbar') ? 1 : 0;
        $navigationItem->requires_auth = $request->has('requires_auth') ? 1 : 0;
        $navigationItem->type = $request->type;
        $navigationItem->visibility_type = $request->visibility_type;
        $navigationItem->save();

        $notify[] = ['success', 'Navigation item updated successfully'];
        return redirect()->route('admin.navigation.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $navigationItem = NavigationItem::findOrFail($id);
        $navigationItem->delete();

        $notify[] = ['success', 'Navigation item deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        $navigationItem = NavigationItem::findOrFail($id);
        $navigationItem->is_active = $request->status;
        $navigationItem->save();

        $notify[] = ['success', 'Navigation item status updated successfully'];
        return back()->withNotify($notify);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:navigation_items,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            NavigationItem::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $notify[] = ['success', 'Navigation order updated successfully'];
        return back()->withNotify($notify);
    }
}
