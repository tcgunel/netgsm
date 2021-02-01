<?php

namespace TCGunel\Netgsm;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NetgsmChannel
{
    use Queueable;

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function sendSms($notifiable, Notification $notification)
    {
        $message = $notification->toVoice($notifiable);

        // Send notification to the $notifiable instance...
    }
}
