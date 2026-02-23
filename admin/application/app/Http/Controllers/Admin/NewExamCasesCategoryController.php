<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\NewExamCasesCategory;
use App\Http\Controllers\Controller;

class NewExamCasesCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'New Exam Cases Category';
        $datalist = $this->newExamCasesCategoryData();

        return view('admin.new-exam-cases-category.index', compact('pageTitle', 'datalist'));
    }

    protected function newExamCasesCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;
        $query = ($scope) ? NewExamCasesCategory::$scope() : NewExamCasesCategory::where('parent_id', 0);

        if ($search) {
            $parentIds = NewExamCasesCategory::where('name', 'like', "%$search%")
                ->where('parent_id', 0)
                ->pluck('id');
            $childIds = NewExamCasesCategory::where('name', 'like', "%$search%")
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
            $childQuery = NewExamCasesCategory::where('parent_id', $parent->id);
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
        $pageTitle = 'Add New Exam Cases Category';
        $categories = NewExamCasesCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.new-exam-cases-category.create', compact('pageTitle', 'categories'));
    }

    public function edit($id){
        $pageTitle = 'Edit New Exam Cases Category';
        $category = NewExamCasesCategory::find($id);
        $categories = NewExamCasesCategory::where('parent_id', 0)->where('status', 1)->get();

        return view('admin.new-exam-cases-category.edit', compact('category', 'pageTitle', 'categories'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $category = new NewExamCasesCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->save();

        $notify[] = ['success', 'New Exam Cases Category added successfully'];

        return redirect()->route('admin.new-exam-cases-category.index')->withNotify($notify);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
        ]);

        $category = NewExamCasesCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->is_premium = $request->is_premium ?? 0;
        $category->update();

        $notify[] = ['success', 'New Exam Cases Category updated successfully'];

        return redirect()->route('admin.new-exam-cases-category.index')->withNotify($notify);
    }

    public function delete($id){
        $category = NewExamCasesCategory::findOrFail($id);
        $category->delete();
        NewExamCasesCategory::where('parent_id', $id)->delete();

        $notify[] = ['success', 'New Exam Cases Category deleted successfully'];
        return back()->withNotify($notify);
    }

    public function togglePremium(Request $request, $id){
        try {
            $category = NewExamCasesCategory::findOrFail($id);
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
