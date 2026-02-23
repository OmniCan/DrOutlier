<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewOsceCategory;
use App\Models\NewOsce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewOsceApiController extends Controller
{
    public function getCategories()
    {
        try {
            $categories = NewOsceCategory::where('parent_id', 0)
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
            $parentCategory = NewOsceCategory::find($categoryId);
            $categoryName = $parentCategory ? $parentCategory->name : '';

            $chapters = NewOsceCategory::where('parent_id', $categoryId)
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

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
            $chapter = NewOsceCategory::find($chapterId);
            $chapterName = $chapter ? $chapter->name : '';

            $items = NewOsce::where('category', $chapterId)
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

            $item = NewOsce::with('categories')->find($request->item_id);

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
