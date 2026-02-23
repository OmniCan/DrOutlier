<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    public function index()
    {

        return view('admin.email.index');
    }

    public function newsletterForm()
    {

        return view('admin.email.newsletter');
    }

    public function sendNewsletter(Request $request)
    {

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $users = User::whereNotNull('email')->get();

            if ($users->isEmpty()) {
                return back()->with('error', 'No registered users found.');
            }
            // foreach ($users as $user) {
            //     if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            //         SendNewsletterJob::dispatch($user->email, $request->subject, $request->message);

            //     } else {
            //         \Log::warning("Invalid email address for user ID: {$user->id}");
            //     }
            // }
            foreach ($users as $user) {
                if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($user->email)->send(new NewsletterMail($request->subject, $request->message));
                } else {

                    \Log::warning("Invalid email address for user ID: {$user->id}");
                }
            }
            return back()->with('success', 'Email sent successfully to all registered users!');
        } catch (\Exception $e) {
            \Log::error('Error sending email' . $e->getMessage());

            return back()->with('error', 'Error sending email  ' );
        }
    }
}
