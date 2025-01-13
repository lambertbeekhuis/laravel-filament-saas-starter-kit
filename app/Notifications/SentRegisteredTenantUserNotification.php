<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SentRegisteredTenantUserNotification extends Notification
{
    use Queueable;

    public User $user;
    public Tenant $tenant;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TenantUser $tenantUser)
    {
        $this->user = $tenantUser->user;
        $this->tenant = $tenantUser->tenant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('mail.sent-registered-tenant-user-notification', [
            'user' => $this->user,
            'tenant' => $this->tenant,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
