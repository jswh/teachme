<?php

namespace App\Notifications;

use App\Channels\LineChannel;
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
        return $this->via;
    }

    protected $via = [WsChannel::class];

    public function viaWs() {
        $this->via = [WsChannel::class];

        return $this;
    }

    public function viaLine() {
        $this->via = [LineChannel::class];

        return $this;
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
        $http = new Client();
        $http->post('https://api.line.me/v2/bot/message/push', [
            'proxy' => getenv('proxy'),
            'headers' => [
                'Authorization' => 'Bearer ' . getenv('LINE_MESSAGE_TOKEN')
            ],
            'json' => [
                'to' => $notifiable->line_user_id,
                "messages" => [
                    ["type" => "text", "text" => $this->text]
                ]
            ]
        ]);
    }

}
