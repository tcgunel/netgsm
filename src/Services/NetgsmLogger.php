<?php

namespace TCGunel\Netgsm\Services;

use Illuminate\Database\Eloquent\Model;
use TCGunel\Netgsm\Models\NetgsmLog;

class NetgsmLogger
{
    public static $netgsm_loggable_type;

    public static $netgsm_loggable_id;

    public static $payload;

    public static $response_type;

    public static $response_message;

    public static $response_code;

    public static $work_type;

    public static function logFor(Model $model)
    {
        if ($model->getKey()) {
            self::$netgsm_loggable_type = get_class($model);

            self::$netgsm_loggable_id = $model->getKey();
        }
    }

    public static function create()
    {
        if (config("netgsm.log")) {

            if (isset(self::$payload['password'])) {

                unset(self::$payload['password']);

            }

            NetgsmLog::create(
                [
                    'netgsm_loggable_id' => self::$netgsm_loggable_id,
                    'netgsm_loggable_type' => self::$netgsm_loggable_type,

                    'work_type' => self::$work_type,
                    'response_type' => self::$response_type,
                    'response_message' => self::$response_message,
                    'response_code' => self::$response_code,
                    'payload' => self::$payload,
                ]
            );

        }
    }
}
