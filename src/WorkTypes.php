<?php

namespace TCGunel\Netgsm;

class WorkTypes
{
    public const SEND_SMS = 'send_sms';

    public const CREDIT_QUERY = 'credit_query';

    public const PACKAGE_CAMPAIGN_QUERY = 'package_campaign_query';

    /**
     * @param string|null $type
     * @return array|string
     */
    public static function readable(?string $type = null)
    {
        $arr = [
            self::SEND_SMS => __('SMS Gönderimi'),
            self::CREDIT_QUERY => __('Kredi Sorgulama'),
            self::PACKAGE_CAMPAIGN_QUERY => __('Paket Kampanya Sorgulama'),
        ];

        return $arr[$type] ?? $arr;
    }
}
