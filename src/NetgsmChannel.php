<?php

namespace TCGunel\Netgsm;

use Illuminate\Notifications\Notification;
use TCGunel\Netgsm\SendSms\SendSms;

class NetgsmChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return string|SendSms
     */
    public function send($notifiable, Notification $notification)
    {
        $netgsm = $notification->toNetgsm($notifiable);

        if ($netgsm instanceof SendSms) {

            $netgsm->execute();

            return $netgsm;

        }

        return '';
    }
}
