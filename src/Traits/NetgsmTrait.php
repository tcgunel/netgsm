<?php

namespace TCGunel\Netgsm\Traits;

use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\SendSms\SendSms;
use SoapClient;
use XMLWriter;

trait NetgsmTrait
{
    /**
     * Available options are http, xml and soap.
     *
     * @var string
     */
    public $service_type;

    /**
     * Properly named keys & values to send API.
     * Array with http and soap,
     * Gets converted to string with XML type request.
     *
     * @var array|string
     */
    public $values_to_send;

    public $work_type;

    public $http_endpoint;

    public $soap_endpoint;

    public $xml_endpoint;

    public $soap_function;

    /**
     * @param string $work_type
     * @return SendSms|NetgsmTrait
     */
    public function setWorkType(string $work_type)
    {
        $this->work_type = $work_type;

        return $this;
    }

    /**
     * @param string $http_endpoint
     * @return SendSms|NetgsmTrait
     */
    public function setHttpEndpoint(string $http_endpoint)
    {
        $this->http_endpoint = $http_endpoint;

        return $this;
    }

    /**
     * @param string $soap_endpoint
     * @return SendSms|NetgsmTrait
     */
    public function setSoapEndpoint(string $soap_endpoint)
    {
        $this->soap_endpoint = $soap_endpoint;

        return $this;
    }

    /**
     * @param string $xml_endpoint
     * @return SendSms|NetgsmTrait
     */
    public function setXmlEndpoint(string $xml_endpoint)
    {
        $this->xml_endpoint = $xml_endpoint;

        return $this;
    }

    /**
     * @param string $soap_function
     * @return SendSms|NetgsmTrait
     */
    public function setSoapFunction(string $soap_function)
    {
        $this->soap_function = $soap_function;

        return $this;
    }

    /**
     * @return SendSms|NetgsmTrait
     */
    protected function setValuesToSend()
    {
        foreach ($this->map[$this->service_type] as $class_key => $request_key) {

            $this->values_to_send[$request_key] = $this->$class_key;

        }

        return $this;
    }

    /**
     * Reads responses and throws error messages taken directly from the documentation
     *
     * @url https://www.netgsm.com.tr/dokuman/
     *
     * @param string $work_type
     * @param string $response
     * @throws \Exception
     */
    protected function handleNetgsmErrors(string $work_type, string $response): void
    {
        switch ($work_type) {
            case 'sms':

                switch ($response) {
                    case 20:
                        throw new \Exception(
                            'Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj
                    karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir.
                    Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter
                    sayılarının hesaplanış şeklini görebilirsiniz.)',
                            422
                        );

                    case 30:
                        throw new \Exception(
                            'Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                        olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                        dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                        web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
                            422
                        );

                    case 40:
                        throw new \Exception(
                            'Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını
                        ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.',
                            422
                        );

                    case 70:
                        throw new \Exception(
                            'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                    birinin eksik olduğunu ifade eder.',
                            422
                        );

                    case 80:
                        throw new \Exception(
                            'Gönderim sınır aşımı.',
                            422
                        );

                    case 100:
                    case 101:
                        throw new \Exception(
                            'Sistem hatası',
                            422
                        );
                }

                break;

            case 'account':
                break;

        }
    }

    /**
     * Recursive function reads nested $xml_array to create corresponding xml tags with XMLWriter reference.
     *
     * @param $arr
     * @param XMLWriter $w
     */
    protected function buildXmlFromArray($arr, XMLWriter $w): void
    {
        foreach ($arr as $key => $el) {

            if (!isset($el['values'])) {

                $w->startElement($key);

                if (is_array($el)) {

                    if (isset($el['attr'])) {

                        foreach ($el['attr'] as $attr => $attr_value) {

                            $w->writeAttribute($attr, $attr_value);

                        }

                        unset($el['attr']);

                    }

                    if (isset($el['cdata'])) {

                        if (isset($el['value'])) {

                            $w->writeCdata($el['value']);

                            unset($el['value']);

                        }

                        unset($el['cdata']);

                    }

                    if (isset($el['value'])) {

                        $w->text($el['value']);

                        unset($el['value']);

                    }

                    if (!empty($el)) {

                        self::buildXmlFromArray($el, $w);

                    }

                } else {

                    $w->text($el);

                }

                $w->endElement();

            } else {

                foreach ($el['values'] as $value) {

                    $w->startElement($key);

                    if (is_array($value)) {

                        self::buildXmlFromArray($value, $w);

                    } else if (isset($el['cdata'])) {

                        $w->writeCdata($value);

                    } else {

                        $w->text($value);

                    }

                    $w->endElement();

                }
            }
        }
    }

    /**
     * @param string $service
     * @param string|string[] $messages
     * @param null $encoding
     * @return int|int[]
     */
    public function calculateMessageLength(string $service, $messages, $encoding = null)
    {
        if (!is_array($messages)) {

            $messages = [$messages];

        }

        foreach ($messages as $k => $message) {

            if ($encoding === null) {

                $encoding = config("netgsm.$service.params.encoding");

            }

            if ($encoding) {

                // These characters counts as 2 each.
                // Refer https://www.netgsm.com.tr/dokuman/#karakter-hesaplama
                preg_match_all('/[çğışĞİŞ]/u', $message, $matches);

            }

            $real_message_length = mb_strlen($message);

            $non_latin_length_addition = isset($matches) ? count($matches[0]) : 0;

            $messages[$k] = $real_message_length + $non_latin_length_addition;

        }

        return count($messages) === 1 ? $messages[0] : $messages;
    }

    /**
     * @param SendSms $instance
     * @return string
     */
    public function getXml(): string
    {
        $xml_array = $this->prepareXmlData();

        return $this->outputXml($xml_array);
    }

    /**
     * Returns an XML string created by XMLWriter.
     *
     * @param $xml_array
     * @return string
     */
    public function outputXml($xml_array): string
    {
        $w = new XMLWriter();

        $w->openMemory();

        $w->startDocument('1.0', 'UTF-8');

        self::buildXmlFromArray($xml_array, $w);

        $this->values_to_send = $w->outputMemory(true);

        return $this->values_to_send;
    }

    public function send(): string
    {
        switch ($this->service_type) {
            case 'http':

                return $this->sendWithHttp();

            case 'xml':

                return $this->sendWithXml();

            default:

                return $this->sendWithSoap();
        }
    }

    public function sendWithHttp(): string
    {
        $this->setServiceType('http')->prepare();

        $response = Http::get($this->http_endpoint, $this->values_to_send);

        $response->throw();

        $this->handleNetgsmErrors($this->work_type, $response->body());

        return $response->body();
    }

    public function sendWithSoap(): string
    {
        $this->setServiceType('soap')->prepare();

        $client = new SoapClient($this->soap_endpoint);

        $result = $client->__soapCall($this->soap_function, array('parameters' => $this->values_to_send));

        $this->handleNetgsmErrors($this->work_type, $result->return);

        return $result->return;
    }

    public function sendWithXml(): string
    {
        $this->setServiceType('xml')->prepare()->getXml();

        $response = Http::withHeaders([
            "Content-Type" => "text/xml;charset=utf-8"
        ])->send('POST', $this->xml_endpoint, [
            'body' => $this->values_to_send
        ]);

        $response->throw();

        $this->handleNetgsmErrors($this->work_type, $response->body());

        return $response->body();
    }
}
