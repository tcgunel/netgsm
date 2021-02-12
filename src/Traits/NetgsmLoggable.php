<?php

namespace TCGunel\Netgsm\Traits;

trait NetgsmLoggable
{
    public function netgsm_logs()
    {
        return $this->morphMany('TCGunel\Netgsm\Models\NetgsmLog', 'netgsm_loggable');
    }
}
