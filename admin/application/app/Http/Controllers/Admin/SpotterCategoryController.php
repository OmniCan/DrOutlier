<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SpottersCategory;
use App\Http\Controllers\Controller;

class SpotterCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'SpottersCategory';
         $datalist = $this->spotterCategoryData();

        return view('admin.spotters-category.index',compact('pageTitle','datalist'));
    }

    protected function spotterCategoryData($scope = null)
    {
        $request = request();
        $search = $request->search;
        $query = ($scope) ? SpottersCategory::$scope() : SpottersCategory::where('parent_id', 0);
        if ($search) {
            $parentIds = SpottersCategory::where('name', 'like', "%$search%")
                ->where('parent_id', 0)
                ->pluck('id');
            $childIds = SpottersCategory::where('name', 'like', "%$search%")
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
            $childQuery = SpottersCategory::where('parent_id', $parent->id);
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

        $pageTitle = 'Add SpottersCategory';

        $categories = SpottersCategory::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.spotters-category.create',compact('pageTitle','categories'));
    }

    public function edit($id){

        $pageTitle = 'Edit Category';

        $category = SpottersCategory::find($id);

        $categories = SpottersCategory::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.spotters-category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);


        $category = new SpottersCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->save();

        $notify[] = ['success', 'Added successfully'];

        return redirect()->route('admin.spotters-category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',
        ]);


        $category = SpottersCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->update();

        $notify[] = ['success', 'Updated successfully'];

        return redirect()->route('admin.spotters-category.index')->withNotify($notify);
    }

    public function delete($id){

        $category = SpottersCategory::findOrFail($id);
        $category->delete();
        SpottersCategory::where('parent_id' , $id)->delete();


        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }


}
