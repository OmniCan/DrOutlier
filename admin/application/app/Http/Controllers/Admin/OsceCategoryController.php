<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\OsceCategory;
use App\Http\Controllers\Controller;

class OsceCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'OsceCategory';
         $datalist = $this->osceCategoryData();

        return view('admin.osce-category.index',compact('pageTitle','datalist'));
    }

    protected function osceCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;

        // Query parent categories (parent_id = NULL or 0)
        $query = ($scope) ? OsceCategory::$scope() : OsceCategory::where(function($q) {
            $q->whereNull('parent_id')->orWhere('parent_id', 0);
        });

        if ($search) {
            $parentIds = OsceCategory::where('name', 'like', "%$search%")
                ->where(function($q) {
                    $q->whereNull('parent_id')->orWhere('parent_id', 0);
                })
                ->pluck('id');
            $childIds = OsceCategory::where('name', 'like', "%$search%")
                ->where('parent_id', '!=', 0)
                ->whereNotNull('parent_id')
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
            $childQuery = OsceCategory::where('parent_id', $parent->id);
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

        $pageTitle = 'Add OsceCategory';

        $categories = OsceCategory::where(function($q) {
            $q->whereNull('parent_id')->orWhere('parent_id', 0);
        })->where('status' , 1)->get();

        return view('admin.osce-category.create',compact('pageTitle','categories'));
    }

    public function edit($id){

        $pageTitle = 'Edit Category';

        $category = OsceCategory::find($id);

        $categories = OsceCategory::where(function($q) {
            $q->whereNull('parent_id')->orWhere('parent_id', 0);
        })->where('status' , 1)->get();

        return view('admin.osce-category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);


        $category = new OsceCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->save();

        $notify[] = ['success', 'Added successfully'];

        return redirect()->route('admin.osce-category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',
        ]);


        $category = OsceCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->update();

        $notify[] = ['success', 'Updated successfully'];

        return redirect()->route('admin.osce-category.index')->withNotify($notify);
    }

    public function delete($id){

        $category = OsceCategory::findOrFail($id);
        $category->delete();
        OsceCategory::where('parent_id' , $id)->delete();


        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }


}
