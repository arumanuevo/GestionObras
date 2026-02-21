<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserApprovedNotification extends Notification // Eliminamos "implements ShouldQueue"
{
    use Queueable;

    public function __construct()
    {
        Log::info('UserApprovedNotification - Constructor llamado');
    }

    public function via($notifiable)
    {
        Log::info('UserApprovedNotification - via method called for user: ' . $notifiable->email);
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        Log::info('UserApprovedNotification - toMail method called for user: ' . $notifiable->email);

        $url = url('/login');

        return (new MailMessage)
            ->subject('Tu cuenta ha sido aprobada - Gestión de Obras')
            ->view('emails.user_approved', [
                'user' => $notifiable,
                'loginUrl' => $url
            ]);
    }
}

