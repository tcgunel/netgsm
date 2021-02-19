<?php

namespace TCGunel\Netgsm\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NetgsmLog
 *
 * @property int $id
 * @property string $response_code
 * @property boolean $response_type
 * @property string $response_message
 * @property string $work_type
 * @property object $payload
 * @property int $netgsm_loggable_id
 * @property string $netgsm_loggable_type
 *
 * @package TCGunel\Netgsm\Models
 */
class NetgsmLog extends Model
{

    protected $fillable = [
        'netgsm_loggable_id',
        'netgsm_loggable_type',
        'response_type',
        'response_code',
        'response_message',
        'payload',
        'work_type',
    ];

    protected $casts = [
        'payload' => 'object',
    ];

    public function netgsm_loggable()
    {
        return $this->morphTo();
    }
}
