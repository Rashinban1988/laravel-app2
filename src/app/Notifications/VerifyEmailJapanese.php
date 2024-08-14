<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmailJapanese extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
        ->subject(Lang::get('「楽まにゅ」メールアドレスの確認'))
        ->greeting(Lang::get('ようこそ！'))
        ->line(Lang::get('アカウント登録ありがとうございます。'))
        ->line(Lang::get('以下のボタンをクリックしてメールアドレスを確認してください。'))
        ->action(Lang::get('メールアドレスを確認'), $verificationUrl)
        ->line(Lang::get('このメールに心当たりがない場合は、無視してください。'))
        ->line(Lang::get('「メールアドレスを確認」ボタンをクリックできない場合は、'))
        ->line(Lang::get('以下のURLをコピーしてウェブブラウザに貼り付けてください：:url', ['url' => $verificationUrl]))
        ->salutation(Lang::get('よろしくお願いいたします。'))
        ->theme('default')
        ->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('X-Mailer-Custom', '© 2024 楽まにゅ. All rights reserved.');
        });
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
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
