<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewTableVivaCategory;
use App\Http\Controllers\Controller;

class NewTableVivaCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'New Table Viva Category';
        $datalist = $this->newTableVivaCategoryData();

        return view('admin.new-table-viva-category.index', compact('pageTitle', 'datalist'));
    }

    protected function newTableVivaCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;
        $query = ($scope) ? NewTableVivaCategory::$scope() : NewTableVivaCategory::where('parent_id', 0);

        if ($search) {
            $parentIds = NewTableVivaCategory::where('name', 'like', "%$search%")
                ->where('parent_id', 0)
                ->pluck('id');
            $childIds = NewTableVivaCategory::where('name', 'like', "%$search%")
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
            $childQuery = NewTableVivaCategory::where('parent_id', $parent->id);
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
        $pageTitle = 'Add New Table Viva Category';
        $categories = NewTableVivaCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.new-table-viva-category.create', compact('pageTitle', 'categories'));
    }

    public function edit($id){
        $pageTitle = 'Edit New Table Viva Category';
        $category = NewTableVivaCategory::find($id);
        $categories = NewTableVivaCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.new-table-viva-category.edit', compact('category', 'pageTitle', 'categories'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $category = new NewTableVivaCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->save();

        $notify[] = ['success', 'New Table Viva Category added successfully'];

        return redirect()->route('admin.new-table-viva-category.index')->withNotify($notify);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
        ]);

        $category = NewTableVivaCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->update();

        $notify[] = ['success', 'New Table Viva Category updated successfully'];

        return redirect()->route('admin.new-table-viva-category.index')->withNotify($notify);
    }

    public function delete($id){
        $category = NewTableVivaCategory::findOrFail($id);
        $category->delete();
        NewTableVivaCategory::where('parent_id', $id)->delete();

        $notify[] = ['success', 'New Table Viva Category deleted successfully'];
        return back()->withNotify($notify);
    }

    public function togglePremium(Request $request, $id){
        try {
            $category = NewTableVivaCategory::findOrFail($id);
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
