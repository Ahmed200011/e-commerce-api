<?php

namespace App\Listeners;

use App\Events\ContactUsEvent;
use App\Helpers\ApiResponse;
use App\Mail\ContactMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ContactUsListeners
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
    public function handle(ContactUsEvent $event): void
    {
        Mail::to('am9695960@gmail.com')->send(new ContactMail($event->data));
    }
}
