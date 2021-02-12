<?php

namespace TCGunel\Netgsm\Traits;

use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\CreditQuery\CreditQuery;
use TCGunel\Netgsm\PackageCampaignQuery\PackageCampaignQuery;
use TCGunel\Netgsm\SendSms\SendSms;
use SoapClient;
use TCGunel\Netgsm\Services\NetgsmLogger;
use TCGunel\Netgsm\ServiceTypes;
use XMLWriter;

trait NetgsmTrait
{
    use ErrorsTrait, SuccessfulResponseTrait;

    /**
     * Properly named keys & values to send API.
     * Array with http and soap,
     * Gets converted to string with XML type request.
     *
     * @var array|string
     */
    protected $values_to_send;

    protected $work_type;

    protected $http_endpoint;

    protected $soap_endpoint;

    protected $xml_endpoint;

    protected $soap_function;

    public $result_code;

    public $result;

    /**
     * @param string $work_type
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setWorkType(string $work_type)
    {
        $this->work_type = $work_type;

        NetgsmLogger::$work_type = $this->work_type;

        return $this;
    }

    /**
     * @param string $http_endpoint
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setHttpEndpoint(string $http_endpoint)
    {
        $this->http_endpoint = $http_endpoint;

        return $this;
    }

    /**
     * @param string $soap_endpoint
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setSoapEndpoint(string $soap_endpoint)
    {
        $this->soap_endpoint = $soap_endpoint;

        return $this;
    }

    /**
     * @param string $xml_endpoint
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setXmlEndpoint(string $xml_endpoint)
    {
        $this->xml_endpoint = $xml_endpoint;

        return $this;
    }

    /**
     * @param string $soap_function
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setSoapFunction(string $soap_function)
    {
        $this->soap_function = $soap_function;

        return $this;
    }

    /**
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    protected function setValuesToSend()
    {
        foreach ($this->map[$this->service_type] as $class_key => $request_key) {

            $this->values_to_send[$request_key] = $this->$class_key;

        }

        NetgsmLogger::$payload = $this->values_to_send;

        return $this;
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
     * @return string
     */
    protected function getXml(): string
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
    protected function outputXml($xml_array): string
    {
        $w = new XMLWriter();

        $w->openMemory();

        $w->startDocument('1.0', 'UTF-8');

        self::buildXmlFromArray($xml_array, $w);

        $this->values_to_send = $w->outputMemory(true);

        return $this->values_to_send;
    }

    public function execute(): string
    {
        switch ($this->service_type) {
            case ServiceTypes::HTTP:

                return $this->executeWithHttp();

            case ServiceTypes::XML:

                return $this->executeWithXml();

            default:

                return $this->executeWithSoap();
        }
    }

    public function executeWithHttp(): string
    {
        $this->setServiceType(ServiceTypes::HTTP)->prepare();

        $response = Http::get($this->http_endpoint, $this->values_to_send);

        $response->throw();

        $this->handleNetgsmErrors($this->work_type, $response->body());

        $this->handleNetgsmResponse($this->work_type, $response->body());

        return $response->body();
    }

    public function executeWithSoap(): string
    {
        $this->setServiceType(ServiceTypes::SOAP)->prepare();

        $client = new SoapClient($this->soap_endpoint);

        $result = $client->__soapCall($this->soap_function, array('parameters' => $this->values_to_send));

        $this->handleNetgsmErrors($this->work_type, $result->return);

        $this->handleNetgsmResponse($this->work_type, $this->work_type);

        return $result->return;
    }

    public function executeWithXml(): string
    {
        $this->setServiceType(ServiceTypes::XML)->prepare()->getXml();

        $response = Http::withHeaders([
            "Content-Type" => "text/xml;charset=utf-8"
        ])->send('POST', $this->xml_endpoint, [
            'body' => $this->values_to_send
        ]);

        $response->throw();

        $this->handleNetgsmErrors($this->work_type, $response->body());

        $this->handleNetgsmResponse($this->work_type, $response->body());

        return $response->body();
    }
}
