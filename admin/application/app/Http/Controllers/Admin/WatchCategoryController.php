<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\WatchCategory; 
use App\Http\Controllers\Controller; 

class WatchCategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Watch and Learn Category';
        $datalist = $this->watchlearnCategoryData();
        return view('admin.watch-category.index',compact('pageTitle','datalist'));
    }
    
 protected function watchlearnCategoryData($scope = null)
{
    $request = request();
    $search = $request->search;
    $query = ($scope) ? WatchCategory::$scope() : WatchCategory::where('parent_id', 0);
    if ($search) {
        $parentIds = WatchCategory::where('name', 'like', "%$search%")
            ->where('parent_id', 0)
            ->pluck('id');
        $childIds = WatchCategory::where('name', 'like', "%$search%")
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
        $childQuery = WatchCategory::where('parent_id', $parent->id);
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

        $categories = WatchCategory::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.watch-category.create',compact('pageTitle','categories'));
    }

    public function edit($id){
 
        $pageTitle = 'Edit Category';
 
        $category = WatchCategory::find($id);
         
        $categories = WatchCategory::where('parent_id' , 0)->where('status' , 1)->get();
        
        return view('admin.watch-category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'  
        ]);


        $watch = new WatchCategory();
        $watch->name = $request->name;
        $watch->parent_id = $request->parent_id ?? 0;
        $watch->color = $request->color;
        $watch->status = $request->status ?? 1;
        $watch->save();

        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.watch-and-learn-category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',  
        ]);
 
        $watch = WatchCategory::find($id);
        $watch->name = $request->name;
        $watch->parent_id = $request->parent_id ?? 0;
        $watch->color = $request->color;
        $watch->status = $request->status ?? 1;
        $watch->update();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.watch-and-learn-category.index')->withNotify($notify); 
    }

    public function delete($id){
  
        $category = WatchCategory::findOrFail($id); 
        $category->delete();
        WatchCategory::where('parent_id' , $id)->delete();
 

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

 
}
