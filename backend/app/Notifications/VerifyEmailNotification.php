<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $userName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $userName)
    {
        $this->token = $token;
        $this->userName = $userName;
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
        $verificationUrl = config('app.frontend_url') . '/verify-email?token=' . $this->token;

        return (new MailMessage)
            ->subject('メールアドレスの確認')
            ->greeting($this->userName . ' 様')
            ->line('ご登録ありがとうございます。')
            ->line('以下のボタンをクリックして、メールアドレスの確認を完了してください。')
            ->action('メールアドレスを確認する', $verificationUrl)
            ->line('このリンクは24時間有効です。')
            ->line('心当たりがない場合は、このメールを無視してください。');
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
