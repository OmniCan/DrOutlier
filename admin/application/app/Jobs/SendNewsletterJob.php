<?php 

namespace App\Jobs;

use App\Mail\NewsletterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue 
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $email;
    public $subject;
    public $message;

    public function __construct($email, $subject, $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle()
    {
        try 
        {
        Mail::to($this->email)->send(new NewsletterMail($this->subject, $this->message));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
