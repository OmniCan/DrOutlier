<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category; 
use App\Http\Controllers\Controller; 

class CategoryController extends Controller
{
    
    public function index()
{
    $pageTitle = 'Category';
    $datalist = $this->categoryData();
    return view('admin.category.index', compact('pageTitle', 'datalist'));
}

protected function categoryData($scope = null)
{
    $request = request();
    $search = $request->search;
    $query = ($scope) ? Category::$scope() : Category::where('parent_id', 0);
    if ($search) {
        $parentIds = Category::where('name', 'like', "%$search%")
            ->where('parent_id', 0)
            ->pluck('id');
        $childIds = Category::where('name', 'like', "%$search%")
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
    $datalist = $query->orderBy('id', 'asc')->paginate(1);
    $datalist->getCollection()->transform(function ($parent) use ($search) {
        
        $childQuery = Category::where('parent_id', $parent->id);
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

        $pageTitle = 'Add Category';

        $categories = Category::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.category.create',compact('pageTitle','categories'));
    }

    public function edit($id){
 
        $pageTitle = 'Edit Category';
 
        $category = Category::find($id);
         
        $categories = Category::where('parent_id' , 0)->where('status' , 1)->get();
        
        return view('admin.category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'  
        ]);


        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->save();

        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',  
        ]);
 
        $category = Category::find($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->color = $request->color;
        $category->status = $request->status ?? 1;
        $category->update();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.category.index')->withNotify($notify); 
    }

    public function delete($id){
  
        $category = Category::findOrFail($id); 
        $category->delete();
        Category::where('parent_id' , $id)->delete();
 

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

 
}
