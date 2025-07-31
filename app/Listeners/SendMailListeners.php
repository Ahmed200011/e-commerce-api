<?php

namespace App\Listeners;

use App\Events\SendMailEvent;
use App\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMailListeners
{
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
    public function handle(SendMailEvent $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeMail($event->user));
    }
}
