<?php

namespace TCGunel\Netgsm\SendSms;

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
     * @var integer|integer[]|string|string[]
     * @required
     */
    public $gsm;

    /**
     * Türkçe karakter desteği isteniyorsa bu alana TR girilmeli, istenmiyorsa null olarak gönderilmelidir.
     * SMS boyu hesabı ve ücretlendirme bu parametreye bağlı olarak değişecektir.
     *
     * @var string
     */
    public $encoding;

    /**
     * Gönderime başlayacağınız tarih. (ddMMyyyyHHmm) * Boş bırakılırsa mesajınız hemen gider.
     *
     * @var string
     */
    public $startdate = '';

    /**
     * İki tarih arası gönderimlerinizde bitiş tarihi.(ddMMyyyyHHmm).
     * * Boş bırakılırsa sistem başlangıç tarihine 21 saat ekleyerek otomatik gönderir.
     *
     * @var string
     */
    public $stopdate = '';

    /**
     * Bayi üyesi iseniz bayinize ait kod.
     *
     * @var string
     */
    public $bayikodu;

    /**
     * İzinli Data filtresi uygulamak istediğiniz mesaj gönderimlerinizde bu parametre "1" değerini almalıdır.
     * Gönderilmediği taktirde filtre uygulanmadan gönderilecektir.İstek yapılırken gönderilmesi zorunludur.
     *
     * @var string
     */
    public $filter = '';

    /**
     * Message sending type for XML,
     * If there are multiple messages and gsm numbers send messages to correspondent numbers in arrays.
     *
     * @var string
     */
    public $type = '1:n';

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
                    'gsm'       => 'gsmno',
                    'encoding'  => 'dil',
                    'startdate' => 'startdate',
                    'stopdate'  => 'stopdate',
                    'filter'    => 'izin',
                ],
                'xml'  => [
                    'username'  => 'usercode',
                    'password'  => 'password',
                    'startdate' => 'startdate',
                    'stopdate'  => 'stopdate',
                    'header'    => 'msgheader',
                    'msg'       => 'message',
                    'gsm'       => 'no',
                    'type'      => 'type',
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
        $this->gsm = $gsm;

        return $this;
    }

    /**
     * @param string $encoding
     * @return Params
     */
    public function setEncoding(string $encoding): Params
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @param string $startdate
     * @return Params
     */
    public function setStartdate(string $startdate): Params
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * @param string $stopdate
     * @return Params
     */
    public function setStopdate(string $stopdate): Params
    {
        $this->stopdate = $stopdate;

        return $this;
    }

    /**
     * @param string $bayikodu
     * @return Params
     */
    public function setBayikodu(string $bayikodu): Params
    {
        $this->bayikodu = $bayikodu;

        return $this;
    }

    /**
     * @param string $filter
     * @return Params
     */
    public function setFilter(string $filter): Params
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param $msg
     * @param $gsm
     * @return Params
     */
    public function setType($msg, $gsm): Params
    {
        if (is_array($msg) && count($msg) > 1 && is_array($gsm) && count($gsm) > 1) {

            $this->type = 'n:n';

        }

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
            ->gsm($this->gsm)
            ->msg($this->msg)
            ->header($this->header, $this->getUsername())
            ->password($this->password)
            ->setType($this->msg, $this->gsm);

        return $this;
    }
}
