<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MunchieCategory; 
use App\Http\Controllers\Controller; 

class MunchiesCategoryController extends Controller
{
  public function index()
{
    $pageTitle = 'Munchie Category';
    $datalist = $this->munchiesCategoryData();
    return view('admin.munchies-category.index', compact('pageTitle', 'datalist'));
}

protected function munchiesCategoryData($scope = null)
{
    $request = request();
    $search = $request->search;
    $query = ($scope) ? MunchieCategory::$scope() : MunchieCategory::where('parent_id', 0);
    if ($search) {
        $parentIds = MunchieCategory::where('name', 'like', "%$search%")
            ->where('parent_id', 0)
            ->pluck('id');
        $childIds = MunchieCategory::where('name', 'like', "%$search%")
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
        $childQuery = MunchieCategory::where('parent_id', $parent->id);
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

        $pageTitle = 'Add Munchies Category';

        $categories = MunchieCategory::where('parent_id' , 0)->where('status' , 1)->get();

        return view('admin.munchies-category.create',compact('pageTitle','categories'));
    }

    public function edit($id){
 
        $pageTitle = 'Edit Category';
 
        $category = MunchieCategory::find($id);
         
        $categories = MunchieCategory::where('parent_id' , 0)->where('status' , 1)->get();
        
        return view('admin.munchies-category.edit', compact('category','pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'color' => 'required'  
        ]);


        $munchies = new MunchieCategory();
        $munchies->name = $request->name;
        $munchies->parent_id = $request->parent_id ?? 0;
        $munchies->color = $request->color;
        $munchies->status = $request->status ?? 1;
        $munchies->save();

        $notify[] = ['success', 'Added successfully'];
        
        return redirect()->route('admin.munchies-category.index')->withNotify($notify);

    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',  
        ]);
 
        $munchies = MunchieCategory::find($id);
        $munchies->name = $request->name;
        $munchies->parent_id = $request->parent_id ?? 0;
        $munchies->color = $request->color;
        $munchies->status = $request->status ?? 1;
        $munchies->update();

        $notify[] = ['success', 'Updated successfully'];
        
        return redirect()->route('admin.munchies-category.index')->withNotify($notify); 
    }

    public function delete($id){
  
        $munchies = Munchiemunchies::findOrFail($id); 
        $munchies->delete();
        MunchieCategory::where('parent_id' , $id)->delete();
 

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

 
}
