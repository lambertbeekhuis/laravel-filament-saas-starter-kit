<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use Sentry\Severity;

class LoginSentryListener
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
    public function handle(LoginEvent $event): void
    {
        $user = $event->user;

        // sent message to Sentry
        if ($user) {
            \Sentry\captureMessage('Logged in: '.$user->email.'.', Severity::info());
        }
    }
}
