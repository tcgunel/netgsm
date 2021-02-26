<?php

namespace TCGunel\Netgsm\SendOtpSms;

use TCGunel\Netgsm\Traits\ParamsTrait;

/**
 * Class Params
 *
 * Params taken directly from Netgsm documentation, SOAP Service part.
 * Param names are adjusted correctly by class for HTTP and XML services.
 *
 * @url https://www.netgsm.com.tr/dokuman/#sms-g%C3%B6nderimi
 * @package TCGunel\Netgsm\Sms
 */
class Params extends FormatParams
{
    use ParamsTrait;

    /**
     * If empty, $username will be used.
     *
     * Sistemde tanımlı olan mesaj başlığınızdır (gönderici adınız). En az 3, en fazla 11 karakterden oluşur.
     *
     * @var string
     */
    public $header;

    /**
     * N:N messages can be send when an array.
     *
     * SMS metninin yer alacağı alandır.
     *
     * @var string|string[]
     * @required
     */
    public $msg;

    /**
     * N:N messages can be send when an array.
     *
     * @var integer|string
     * @required
     */
    public $gsm;

    public function __construct()
    {
        $this
            ->setRequired([
                'username' => 'Kullanıcı Adı',
                'password' => 'Şifre',
                'msg'      => 'Mesaj içeriği',
                'gsm'      => 'Gönderilecek telefon numarası',
            ])
            ->setMap([
                'http' => [
                    'username'  => 'usercode',
                    'password'  => 'password',
                    'header'    => 'msgheader',
                    'msg'       => 'message',
                    'gsm'       => 'no',
                ],
                'xml'  => [
                    'username'  => 'usercode',
                    'password'  => 'password',
                    'header'    => 'msgheader',
                    'msg'       => 'message',
                    'gsm'       => 'no',
                ],
            ]);
    }

    /**
     * @param string $header
     * @return Params
     */
    public function setHeader(string $header): Params
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param string|string[] $msg
     * @return Params
     */
    public function setMsg($msg): Params
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @param int|integer[]|string|string[] $gsm
     * @return Params
     */
    public function setGsm($gsm): Params
    {
        $gsm = preg_replace('/[\D]/','', $gsm);

        $this->gsm = $gsm;

        return $this;
    }

    /**
     * @param $service_type
     * @return $this
     * @throws \Exception
     */
    public function formatParamsByService($service_type): Params
    {
        $this->service_type = $service_type;

        $this
            ->msg($this->msg)
            ->header($this->header, $this->getUsername())
            ->password($this->password);

        return $this;
    }
}
