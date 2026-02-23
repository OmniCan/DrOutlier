<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TheoryNotesCategory;
use App\Http\Controllers\Controller;

class TheoryNotesCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Theory Notes Category';
        $datalist = $this->theoryNotesCategoryData();

        return view('admin.theory-notes-category.index', compact('pageTitle', 'datalist'));
    }

    protected function theoryNotesCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;
        $query = ($scope) ? TheoryNotesCategory::$scope() : TheoryNotesCategory::where('parent_id', 0);

        if ($search) {
            $parentIds = TheoryNotesCategory::where('name', 'like', "%$search%")
                ->where('parent_id', 0)
                ->pluck('id');
            $childIds = TheoryNotesCategory::where('name', 'like', "%$search%")
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
            $childQuery = TheoryNotesCategory::where('parent_id', $parent->id);
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
        $pageTitle = 'Add Theory Notes Category';
        $categories = TheoryNotesCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.theory-notes-category.create', compact('pageTitle', 'categories'));
    }

    public function edit($id){
        $pageTitle = 'Edit Theory Notes Category';
        $category = TheoryNotesCategory::find($id);
        $categories = TheoryNotesCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.theory-notes-category.edit', compact('category', 'pageTitle', 'categories'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $category = new TheoryNotesCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->save();

        $notify[] = ['success', 'Theory Notes Category added successfully'];

        return redirect()->route('admin.theory-notes-category.index')->withNotify($notify);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
        ]);

        $category = TheoryNotesCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->update();

        $notify[] = ['success', 'Theory Notes Category updated successfully'];

        return redirect()->route('admin.theory-notes-category.index')->withNotify($notify);
    }

    public function delete($id){
        $category = TheoryNotesCategory::findOrFail($id);
        $category->delete();
        TheoryNotesCategory::where('parent_id', $id)->delete();

        $notify[] = ['success', 'Theory Notes Category deleted successfully'];
        return back()->withNotify($notify);
    }

    public function togglePremium(Request $request, $id){
        try {
            $category = TheoryNotesCategory::findOrFail($id);
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
