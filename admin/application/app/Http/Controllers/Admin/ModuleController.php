<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Modules';
        $modules = Module::orderBy('sort_order')->paginate(getPaginate());
        return view('admin.modules.index', compact('pageTitle', 'modules'));
    }

    public function create()
    {
        $pageTitle = 'Create Module';
        return view('admin.modules.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:modules',
            'display_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:modules',
            'frontend_url' => 'nullable|string|max:255',
            'admin_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $module = new Module();
        $module->name = $request->name;
        $module->display_name = $request->display_name;
        $module->slug = $request->slug;
        $module->frontend_url = $request->frontend_url;
        $module->admin_url = $request->admin_url;
        $module->description = $request->description;
        $module->icon = $request->icon;
        $module->sort_order = $request->sort_order ?? 0;
        $module->is_active = $request->has('is_active') ? 1 : 0;
        $module->save();

        $notify[] = ['success', 'Module created successfully'];
        return redirect()->route('admin.modules.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Module';
        $module = Module::findOrFail($id);
        return view('admin.modules.edit', compact('pageTitle', 'module'));
    }

    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name,' . $id,
            'display_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:modules,slug,' . $id,
            'frontend_url' => 'nullable|string|max:255',
            'admin_url' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $module->name = $request->name;
        $module->display_name = $request->display_name;
        $module->slug = $request->slug;
        $module->frontend_url = $request->frontend_url;
        $module->admin_url = $request->admin_url;
        $module->description = $request->description;
        $module->icon = $request->icon;
        $module->sort_order = $request->sort_order ?? 0;
        $module->is_active = $request->has('is_active') ? 1 : 0;
        $module->save();

        $notify[] = ['success', 'Module updated successfully'];
        return redirect()->route('admin.modules.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $module = Module::findOrFail($id);

        // Check if module is associated with any active plans
        if ($module->plans()->where('is_active', true)->exists()) {
            $notify[] = ['error', 'Cannot delete module that is associated with active plans'];
            return back()->withNotify($notify);
        }

        $module->delete();

        $notify[] = ['success', 'Module deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $module->is_active = $request->status;
        $module->save();

        $notify[] = ['success', 'Module status updated successfully'];
        return back()->withNotify($notify);
    }

    public function moduleData()
    {
        $modules = Module::orderBy('sort_order')->get();
        return response()->json($modules);
    }
}
