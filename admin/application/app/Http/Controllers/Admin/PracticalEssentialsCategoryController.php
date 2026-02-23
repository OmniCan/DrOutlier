<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PracticalEssentialsCategory;
use App\Http\Controllers\Controller;

class PracticalEssentialsCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Practical Essentials Category';
        $datalist = $this->practicalEssentialsCategoryData();

        return view('admin.practical-essentials-category.index', compact('pageTitle', 'datalist'));
    }

    protected function practicalEssentialsCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;
        $query = ($scope) ? PracticalEssentialsCategory::$scope() : PracticalEssentialsCategory::where('parent_id', 0);

        if ($search) {
            $parentIds = PracticalEssentialsCategory::where('name', 'like', "%$search%")
                ->where('parent_id', 0)
                ->pluck('id');
            $childIds = PracticalEssentialsCategory::where('name', 'like', "%$search%")
                ->where('parent_id', '!=', 0)
                ->pluck('parent_id')
                ->unique();
            $allParentIds = $parentIds->merge($childIds)->unique();

            if ($allParentIds->isNotEmpty()) {
                $query = $query->whereIn('id', $allParentIds);
            } else {
                $query = $query->where('id', 0);
            }
        }

        $datalist = $query->orderBy('id', 'asc')->paginate(getPaginate());
        $datalist->getCollection()->transform(function ($parent) use ($search) {
            $childQuery = PracticalEssentialsCategory::where('parent_id', $parent->id);
            if ($search) {
                $childQuery->where('name', 'like', "%$search%");
            }
            $parent->child = $childQuery->orderBy('id', 'asc')->get();
            return $parent;
        });

        if ($search) {
            $datalist->getCollection()->filter(function ($parent) use ($search) {
                $matchesParent = str_contains(strtolower($parent->name), strtolower($search));
                $hasMatchingChildren = $parent->child->isNotEmpty();
                return $matchesParent || $hasMatchingChildren;
            });
        }

        return $datalist;
    }

    public function create(){
        $pageTitle = 'Add Practical Essentials Category';
        $categories = PracticalEssentialsCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.practical-essentials-category.create', compact('pageTitle', 'categories'));
    }

    public function edit($id){
        $pageTitle = 'Edit Practical Essentials Category';
        $category = PracticalEssentialsCategory::find($id);
        $categories = PracticalEssentialsCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.practical-essentials-category.edit', compact('category', 'pageTitle', 'categories'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $category = new PracticalEssentialsCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->save();

        $notify[] = ['success', 'Practical Essentials Category added successfully'];

        return redirect()->route('admin.practical-essentials-category.index')->withNotify($notify);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
        ]);

        $category = PracticalEssentialsCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->update();

        $notify[] = ['success', 'Practical Essentials Category updated successfully'];

        return redirect()->route('admin.practical-essentials-category.index')->withNotify($notify);
    }

    public function delete($id){
        $category = PracticalEssentialsCategory::findOrFail($id);
        $category->delete();
        PracticalEssentialsCategory::where('parent_id', $id)->delete();

        $notify[] = ['success', 'Practical Essentials Category deleted successfully'];
        return back()->withNotify($notify);
    }

    public function togglePremium(Request $request, $id){
        try {
            $category = PracticalEssentialsCategory::findOrFail($id);
            $category->is_premium = $category->is_premium == 1 ? 0 : 1;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Premium status updated successfully.',
                'is_premium' => $category->is_premium
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update premium status.'
            ]);
        }
    }
}
