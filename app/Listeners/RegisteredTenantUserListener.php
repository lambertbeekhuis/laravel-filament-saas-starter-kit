<?php

namespace App\Listeners;

use App\Events\RegisteredTenantUser;
use App\Notifications\SentInvitationToUserNotification;
use App\Notifications\SentRegisteredTenantUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegisteredTenantUserListener
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisteredTenantUser $event): void
    {
        $tenantUser = $event->tenantUser;
        $user = $tenantUser->user;

        $user->notify(new SentRegisteredTenantUserNotification($event->tenantUser));
    }
}
