<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Test extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public function __construct(public ?string $emails = null) {}

    public function via(object $notifiable): array
    {
        return setNotificationChannels($notifiable, 'test');
    }

    public function toMail(): MailMessage
    {
        $mail = new MailMessage();
        $mail->subject('Publify: Test Email');
        $mail->view('emails.test');

        return $mail;
    }

    public function toDiscord(): string
    {
        $message = 'Publify: This is a test Discord notification from Publify.';
        $message .= "\n\n";
        $message .= '[Go to your dashboard]('.base_url().')';

        return $message;
    }

    public function toTelegram(): array
    {
        return [
            'message' => 'Publify: This is a test Telegram notification from Publify.',
            'buttons' => [
                [
                    'text' => 'Go to your dashboard',
                    'url' => base_url(),
                ],
            ],
        ];
    }
}
