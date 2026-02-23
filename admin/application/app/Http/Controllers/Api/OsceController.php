<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Osce;
use App\Models\OsceBookmark;
use App\Models\OsceCategory;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Illuminate\Support\Facades\Validator;
use Auth;

class OsceController extends Controller
{


    public function index()
    {
        $pageTitle = 'Osce List';

        $datalist = Osce::with('question')->orderBy('created_at', 'desc')->paginate(20);


        return response()->json([
            'data' => [
                'datalist' => $datalist,
            ]
        ]);
    }

    public function indexAll()
    {
        $pageTitle = 'Osce List';

        $datalist = Osce::with('question')->orderBy('created_at', 'desc')->get();


        return response()->json([
            'data' => [
                'datalist' => $datalist,
            ]
        ]);
    }


    public function changeBookOsce(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'osce_id' => 'required',
        ]);


        $bookmark = OsceBookmark::where('user_id', $request->user_id)->where('osce_id', $request->osce_id)->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'UnSaved successfully !!',
            ]);
        } else {
            $newBookmark = OsceBookmark::create([
                'user_id' => $request->user_id,
                'osce_id' => $request->osce_id,
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

    public function getBookOsce(Request $request)
    {


        $blogIds = OsceBookmark::where('user_id', $request->user_id)->pluck('osce_id');

        $osces = Osce::with('question')->whereIn('id', $blogIds)->paginate(20);

        $osceItems = $osces->items();
        return response()->json([
            'status' => 'success',
            'data' => [
                'list' => $osces,
            ]
        ]);
    }

    public function osceDetails(Request $request)
    {

        $datalist = Osce::with('question')->where('id', $request->id)->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'datalist' => $datalist,
            ]
        ]);
    }

    public function showOsceDetails($id)
    {

        $datalist = Osce::with('question')->find($id);

        if ($datalist) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'datalist' => $datalist,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'datalist' => null,
                ]
            ]);
        }
    }

    // Get categories with child categories
    public function categoryList()
    {
        $user = Auth::user();

        // Get parent categories (parent_id is NULL or 0)
        $datalist = OsceCategory::where('status', 1)
            ->where(function($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->paginate(20);

        $datalist = $datalist->map(function ($result) use ($user) {
            $result->child = OsceCategory::where('parent_id', $result->id)
                ->where('status', 1)
                ->get()
                ->map(function ($child) use ($user) {
                    return $child;
                });

            return $result;
        });

        return response()->json([
            'status' => 'success',
            'data' => $datalist
        ]);
    }

    // Get OSCEs by category
    public function categoryOsces(Request $request)
    {
        $category = OsceCategory::find($request->category_id);

        $osces = Osce::with('question')
            ->where('category', $request->category_id)
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => [
                'list' => $osces,
                'category' => $category
            ]
        ]);
    }

    // Get single OSCE by ID
    public function getOsceById(Request $request)
    {
        $osce = Osce::with('question')->find($request->osce_id);

        if ($osce) {
            return response()->json([
                'status' => 'success',
                'data' => $osce
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'OSCE not found',
                'data' => null
            ]);
        }
    }
}
