<?php

namespace App\Notifications;

use App\Channels\WsChannel;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SimpleNotification extends Notification
{
    use Queueable;
    protected $text;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WsChannel::class];
    }

    public function toWs($notifiable)
    {
        $http = new Client();
        $http->post(getenv('NOTIFICATION_SERVER'), [
            'form_params' => [
                'secret'    => getenv('NOTIFICATION_SECRET'),
                'to_chat_id' => $notifiable->chat_id,
                'message' => $this->text
            ]
        ]);

    }

    public function toLine($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

}
