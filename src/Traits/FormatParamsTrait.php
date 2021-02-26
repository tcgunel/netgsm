<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\SendSms\Params as SendSmsParams;
use TCGunel\Netgsm\CreditQuery\Params as CreditQueryParams;
use TCGunel\Netgsm\PackageCampaignQuery\Params as PackageCampaignQueryParams;
use TCGunel\Netgsm\SendOtpSms\Params as SendOtpSmsParams;
use TCGunel\Netgsm\ServiceTypes;

trait FormatParamsTrait
{
    /**
     * @var string
     * @required
     */
    protected $service_type;

    /**
     * @param string $service_type
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|SendOtpSmsParams|FormatParamsTrait
     */
    protected function setServiceType(string $service_type)
    {
        $this->service_type = $service_type;

        return $this;
    }

    /**
     * @param string $password
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|SendOtpSmsParams|FormatParamsTrait
     */
    protected function password(string &$password)
    {
        switch ($this->service_type) {
            case ServiceTypes::HTTP:

                $password = urlencode($password);

                break;
        }

        return $this;
    }
}
