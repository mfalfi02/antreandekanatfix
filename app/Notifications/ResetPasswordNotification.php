<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected string $email
    ) {
    }

    /**
     * Notifikasi ini dikirim lewat email agar user bisa mereset password dari link aman yang bertanggal kedaluwarsa.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Membuat email reset password dengan pesan yang lebih jelas dan tombol aksi yang menonjol.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
            [
                'token' => $this->token,
                'email' => $this->email,
            ]
        );

        return (new MailMessage)
            ->subject('Reset Password Sistem Antrean Dekanat')
            ->markdown('mail.reset-password', [
                'name' => $notifiable->name ?? 'User',
                'url' => $resetUrl,
                'expireMinutes' => config('auth.passwords.users.expire', 60),
            ]);
    }
}
