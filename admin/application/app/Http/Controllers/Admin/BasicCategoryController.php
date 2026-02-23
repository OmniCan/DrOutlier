<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BasicCategory; 
use App\Http\Controllers\Controller; 

class BasicCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Basic Category';
         $datalist = $this->basicCategoryData();
        return view('admin.basic-category.index',compact('pageTitle','datalist'));
    }

 protected function basicCategoryData($scope = null)
{
    $request = request();
    $search = $request->search;
    $query = ($scope) ? BasicCategory::$scope() : BasicCategory::where('parent_id', 0);
    if ($search) {
        $parentIds = BasicCategory::where('name', 'like', "%$search%")
            ->where('parent_id', 0)
            ->pluck('id');
        $childIds = BasicCategory::where('name', 'like', "%$search%")
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
    $datalist = $query->orderBy('id', 'asc')->paginate(2); 
    $datalist->getCollection()->transform(function ($parent) use ($search) {
        $childQuery =BasicCategory::where('parent_id', $parent->id);
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

        $pageTitle = 'Add Basic Category';

        $categories = BasicCategory::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.basic-category.create',compact('pageTitle','categories'));
    }

    public function edit($id){
 
        $pageTitle = 'Edit Category';
 
        $category = BasicCategory::find($id);
         
        $categories = BasicCategory::where('parent_id' , 0)->where('status' , 1)->get();
        
        return view('admin.basic-category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'  
        ]);


        $category = new BasicCategory();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->save();

        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.basic-category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',  
        ]);
 
        $category = BasicCategory::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->update();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.basic-category.index')->withNotify($notify); 
    }

    public function delete($id){
  
        $category = BasicCategory::findOrFail($id); 
        $category->delete();
        BasicCategory::where('parent_id' , $id)->delete();
 

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

 
}
