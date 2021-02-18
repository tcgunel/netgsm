<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\Models\NetgsmLog;

trait NetgsmLoggable
{
    public function netgsm_logs()
    {
        return $this->morphMany(NetgsmLog::class, 'netgsm_loggable');
    }
}
