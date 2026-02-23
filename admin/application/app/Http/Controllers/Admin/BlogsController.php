<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MemberOfParliament;
use App\Models\Blog;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Notifications\SendPushNotification;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class BlogsController extends Controller
{

    public function via($notifiable)
    {
        return ['firebase'];
    }
    public function index()
    {
        $pageTitle = 'Note List';
        $bloglist = $this->blogData();
        return view('admin.blog.index', compact('pageTitle', 'bloglist'));
    }

    protected function blogData($scope = null)
    {

        $query = ($scope)
            ? Blog::$scope()
            : Blog::with(['categories']);

        $request = request();
        if ($request->search) {
            $search = $request->search;
            $query = $query->where('title', 'like', "%$search%");
        }
        $bloglist = $query->orderBy('sort_order', 'ASC')->paginate();

        return $bloglist;
    }

    public function create()
    {
        $pageTitle = 'Add Note';

        $datalist = Category::where('parent_id', 0)->where('status', 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = Category::where('parent_id', $result->id)->where('status', 1)->get();
            return $result;
        });
        // $datalist = Category::where('parent_id', '!=' , 0)->where('status' , 1)->get();
        // $datalist = Category::where('status' , 1)->get();

        return view('admin.blog.create', compact('pageTitle', 'datalist'));
    }

    public function store(Request $request)
    {

        $user_token = User::all();


        $deviceTokens = [];
        foreach ($user_token as $token) {
            array_push($deviceTokens, $token->fcm_token);
        }

        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $blog = new Blog();
        $blog->category = $request->category;
        $blog->title = $request->title;
        $blog->sort_order = $request->sort_order;
        $blog->content = $request->content;
        $blog->save();

        (new FirebaseMessage)->withTitle('Hey, ', 'hello')->withBody('New Events Added!')->asNotification($deviceTokens);

        $notify[] = ['success', 'Added successfully'];

        return redirect()->route('admin.blogs.blog-index')->withNotify($notify);
    }


    public function edit($id)
    {

        $pageTitle = 'Update Note';

        $datalist = Category::where('parent_id', 0)->where('status', 1)->get();

        $datalist = $datalist->map(function ($result) {
            $result->child = Category::where('parent_id', $result->id)->where('status', 1)->get();
            return $result;
        });


        $blog = Blog::find($id);
        return view('admin.blog.edit', compact('pageTitle', 'blog', 'datalist'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);


        $blog = Blog::findOrFail($id);
        $blog->category = $request->category;
        $blog->title = $request->title;
        $blog->sort_order = $request->sort_order;
        $blog->content = $request->content;
        $blog->save();

        $notify[] = ['success', 'Updated successfully'];

        return redirect()->route('admin.blogs.blog-index')->withNotify($notify);
    }

    public function updateSortOrder(Request $request)
    {

        $request->validate([
            'sort_order' => 'required|integer',
        ]);

        try {
            $blog = Blog::findOrFail($request->status_id);
            $blog->sort_order = $request->sort_order;
            $blog->save();


            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully.',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update sort order. Please try again.',
            ]);
        }
    }



    public function delete($id)
    {

        $blog = Blog::findOrFail($id);
        $blog->delete();



        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }

    public function updateToken(Request $request)
    {
        try {
            $request->user()->update(['fcm_token' => $request->token]);
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    // public function UpdateSortOrder(Request $request)
    // {

    //     $blog = Blog::find($request->status_id);

    //     if (!$blog) {
    //         return response()->json(['error' => 'Blog not found.'], 404);
    //     }

    //     $blog->sort_order = $request->sort_order;
    //     $blog->save();

    //     return response()->json([
    //         'success' => true
    //     ]);
    // }
}
