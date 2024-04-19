<?php

namespace TCGunel\Netgsm\CreditQuery;

use TCGunel\Netgsm\Traits\ParamsTrait;

/**
 * Class Params
 *
 * Params taken directly from Netgsm documentation, SOAP Service part.
 * Param names are adjusted correctly by class for HTTP and XML services.
 *
 * @url https://www.netgsm.com.tr/dokuman/#kredi-sorgulama
 * @package TCGunel\Netgsm\Sms
 */
class Params extends FormatParams
{
    use ParamsTrait;

    /**
     * Sadece XML için 2 sabit olarak gönderilir. Zorunlu Parametre.
     *
     * @var string
     * @required
     */
    protected $stip = 2;

    public function __construct()
    {
        $this
            ->setRequired([
                'username' => 'Kullanıcı Adı',
                'password' => 'Şifre',
            ])
            ->setMap([
                'http' => [
                    'username'  => 'usercode',
                    'password'  => 'password',
                ],
                'xml'  => [
                    'username'  => 'usercode',
                    'password'  => 'password',
                    'stip'      => 'stip',
                ],
            ]);
    }

    /**
     * @param $service_type
     * @return $this
     * @throws \Exception
     */
    protected function formatParamsByService($service_type): Params
    {
        $this->service_type = $service_type;

        $this->password($this->password);

        return $this;
    }
}
