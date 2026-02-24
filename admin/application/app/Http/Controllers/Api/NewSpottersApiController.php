<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewSpottersCategory;
use App\Models\NewSpotter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewSpottersApiController extends Controller
{
    // Get all parent categories (categories with parent_id = 0)
    public function getCategories()
    {
        try {
            $categories = NewSpottersCategory::where('parent_id', 0)
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get all chapters (sub-categories) for a given parent category
    public function getChapters(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $categoryId = $request->category_id;

            // Get parent category name
            $parentCategory = NewSpottersCategory::find($categoryId);
            $categoryName = $parentCategory ? $parentCategory->name : '';

            // Get all child categories (chapters)
            $chapters = NewSpottersCategory::where('parent_id', $categoryId)
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            // Add first item ID for each chapter for direct navigation
            $chapters = $chapters->map(function ($chapter) {
                $firstItem = NewSpotter::where('category', $chapter->id)
                    ->orderBy('sort_order', 'ASC')
                    ->first();
                
                $chapter->first_item_id = $firstItem ? $firstItem->id : null;
                return $chapter;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'category_name' => $categoryName,
                    'chapters' => $chapters
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch chapters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get all content items for a specific chapter
    public function getItemsByChapter(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chapter_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $chapterId = $request->chapter_id;

            // Get chapter info
            $chapter = NewSpottersCategory::find($chapterId);
            $chapterName = $chapter ? $chapter->name : '';

            // Get all items for this chapter, ordered by sort_order
            $items = NewSpotter::where('category', $chapterId)
                ->orderBy('sort_order', 'ASC')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'chapter_name' => $chapterName,
                    'items' => $items
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch content items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get single content item by ID
    public function getItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item = NewSpotter::with('categories')->find($request->item_id);

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $item
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
