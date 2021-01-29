<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\SendSms\FormatParams;

trait FormatParamsTrait
{
    /**
     * @var string
     * @required
     */
    public $service_type;

    /**
     * @param string $service_type
     * @return FormatParams|FormatParamsTrait
     */
    public function setServiceType(string $service_type): FormatParams
    {
        $this->service_type = $service_type;

        return $this;
    }

    /**
     * @param string $password
     * @return FormatParams|FormatParamsTrait
     */
    protected function password(string &$password): FormatParams
    {
        switch ($this->service_type) {
            case 'http':

                $password = urlencode($password);

                break;
        }

        return $this;
    }
}
