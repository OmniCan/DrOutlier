<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewOsceCategory;
use App\Models\NewOsce;
use App\Models\OsceBookmark;
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

            // Add first item ID for each chapter for direct navigation
            $chapters = $chapters->map(function ($chapter) {
                $firstItem = NewOsce::where('category', $chapter->id)
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

    // Toggle bookmark for New OSCE item
    public function changeBookmark(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'item_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bookmark = OsceBookmark::where('user_id', $request->user_id)
                ->where('osce_id', $request->item_id)
                ->first();

            if ($bookmark) {
                $bookmark->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'UnSaved successfully !!',
                ], 200);
            }

            $newBookmark = OsceBookmark::create([
                'user_id' => $request->user_id,
                'osce_id' => $request->item_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Saved successfully !!',
                'data' => ['list' => $newBookmark]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update bookmark',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get bookmarks for New OSCE (reads from shared bookmark table)
    public function getBookmarks(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ids = OsceBookmark::where('user_id', $request->user_id)->pluck('osce_id');
            $items = NewOsce::whereIn('id', $ids)->get();

            return response()->json([
                'status' => 'success',
                'data' => ['list' => $items]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bookmarks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
