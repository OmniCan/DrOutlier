<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SpottersCategory;
use App\Models\Spotter;
use App\Models\Blog;
use App\Models\SpotterBookmark;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SpottersController extends Controller
{
    // public function index(){
    //     $pageTitle = 'Category';

    //     $datalist = Category::where('status' , 1)->get();


    //     return response()->json([
    //         'status'=>'success',
    //         'data'=>[
    //             'datalist'=>$datalist,
    //         ]
    //     ]);
    // }


    public function index(Request $request){

        $datalist = Spotter::with('categories')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status'=>'success',
            'data'=>[
                'datalist'=>$datalist,
            ]
        ]);

    }
    public function allList(Request $request){

        $datalist = Spotter::with('categories')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status'=>'success',
            'data'=>[
                'datalist'=>$datalist,
            ]
        ]);

    }

    public function spottersDetails(Request $request){

        $datalist = Spotter::with('categories')->where('id' , $request->id)->first();

        return response()->json([
            'status'=>'success',
            'data'=>[
                'datalist'=>$datalist,
            ]
        ]);
    }


    public function changeBookStatus(Request $request){


        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'spotter_id'=>'required',
        ]);

        $bookmark = SpotterBookmark::where('user_id', $request->user_id)->where('spotter_id', $request->spotter_id)->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else {
            $newBookmark = SpotterBookmark::create([
                'user_id' => $request->user_id,
                'spotter_id' => $request->spotter_id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Saved successfully !!',
                'data' => [
                    'list' => $newBookmark,
                ]
            ]);
        }

    }

     public function getBookStatus(Request $request){


        $blogIds = SpotterBookmark::where('user_id', $request->user_id)->pluck('spotter_id');
        $spotters = Spotter::whereIn('id', $blogIds)->paginate(20);

         return response()->json([
             'status' => 'success',
             'data' => [
                 'list' => $spotters,
             ]
         ]);

     }

     /**
      * Get all active spotter categories (parent categories only)
      *
      * @return \Illuminate\Http\JsonResponse
      */
     public function categories()
     {
         try {
             $categories = SpottersCategory::where('status', 1)
                 ->where('parent_id', 0)
                 ->orderBy('id', 'ASC')
                 ->get();

             return response()->json([
                 'status' => 'success',
                 'message' => 'Categories retrieved successfully',
                 'data' => $categories
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Error fetching categories',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

     /**
      * Get chapters (sub-categories) by parent category ID
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function chapters(Request $request)
     {
         try {
             $categoryId = $request->input('category_id');

             if (!$categoryId) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'Category ID is required'
                 ], 400);
             }

             // Get parent category name
             $category = SpottersCategory::where('id', $categoryId)
                 ->where('status', 1)
                 ->where('parent_id', 0)
                 ->first();

             if (!$category) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'Category not found'
                 ], 404);
             }

             // Get chapters (child categories) for this parent category
             $chapters = SpottersCategory::where('status', 1)
                 ->where('parent_id', $categoryId)
                 ->orderBy('id', 'ASC')
                 ->get();

             return response()->json([
                 'status' => 'success',
                 'message' => 'Chapters retrieved successfully',
                 'data' => [
                     'category_name' => $category->name,
                     'chapters' => $chapters
                 ]
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Error fetching chapters',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

     /**
      * Get spotters by category ID
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function listByCategory(Request $request)
     {
         try {
             $categoryId = $request->input('category_id');

             if (!$categoryId) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'Category ID is required'
                 ], 400);
             }

             // Get category name
             $category = SpottersCategory::where('id', $categoryId)
                 ->where('status', 1)
                 ->first();

             if (!$category) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'Category not found'
                 ], 404);
             }

             // Get spotters for this category
             // Check both 'category' and 'category_id' columns for compatibility
             $spotters = Spotter::with('categories')
                 ->where(function($query) use ($categoryId) {
                     $query->where('category', $categoryId)
                           ->orWhere('category_id', $categoryId);
                 })
                 ->orderBy('sort_order', 'ASC')
                 ->orderBy('created_at', 'DESC')
                 ->get();

             return response()->json([
                 'status' => 'success',
                 'message' => 'Spotters retrieved successfully',
                 'data' => [
                     'category_name' => $category->name,
                     'category' => $category,
                     'datalist' => $spotters
                 ]
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Error fetching spotters',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

}
