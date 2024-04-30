<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmail
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $verificationCode = random_int(100000, 999999);
        Mail::to($event->user->email)->send(new VerificationCodeMail($verificationCode));
        $event->user->verification_code = $verificationCode;
        $event->user->save();
    }
}
