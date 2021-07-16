<?php

namespace TCGunel\Netgsm\Traits;

use CodeDredd\Soap\Facades\Soap;
use GuzzleHttp\Psr7\Header;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\CreditQuery\CreditQuery;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Exceptions\NetgsmServiceNotAvailableException;
use TCGunel\Netgsm\PackageCampaignQuery\PackageCampaignQuery;
use TCGunel\Netgsm\SendSms\SendSms;
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

    protected $request_client;

    public $result_code;

    public $result;

    /**
     * @return Soap|Http
     */
    public function getRequestClient()
    {
        return $this->request_client;
    }

    /**
     * @param null|Soap|Http $request_client
     * @param null|string $service_type
     * @return SendSms|CreditQuery|PackageCampaignQuery|NetgsmTrait
     */
    public function setRequestClient($request_client = null, $service_type = null)
    {
        $this->request_client = $request_client;

        if (is_null($this->request_client)) {

            switch ($service_type) {
                case ServiceTypes::SOAP:

                    $this->request_client = Soap::class;

                    break;
                default:

                    $this->request_client = Http::class;

                    break;
            }

        }

        return $this;
    }

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
    public function outputXml($xml_array): string
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

            case ServiceTypes::SOAP:

                return $this->executeWithSoap();

            default:

                return $this->executeWithXml();

        }
    }

    /**
     * @return string
     * @throws \Illuminate\Http\Client\RequestException|\Exception
     */
    public function executeWithHttp(): string
    {
        $this
            ->setServiceType(ServiceTypes::HTTP)
            ->checkServiceAvailability(ServiceTypes::HTTP)
            ->prepare();

        $response = $this->getRequestClient()::get($this->http_endpoint, $this->values_to_send);

        $response->throw();

        $this->handleNetgsmErrors($this->work_type, $response->body());

        $this->handleNetgsmResponse($this->work_type, $response->body());

        return $response->body();
    }

    /**
     * @return string
     * @throws \CodeDredd\Soap\Exceptions\RequestException|\Exception
     */
    public function executeWithSoap(): string
    {
        $this
            ->setServiceType(ServiceTypes::SOAP)
            ->checkServiceAvailability(ServiceTypes::SOAP)
            ->prepare();

        $result = $this->request_client::baseWsdl($this->soap_endpoint)
            ->call($this->soap_function, $this->values_to_send)
            ->throw()
            ->json();

        $this->handleNetgsmErrors($this->work_type, $result['return']);

        $this->handleNetgsmResponse($this->work_type, $result['return']);

        return $result['return'];
    }

    /**
     * @return string
     * @throws \Illuminate\Http\Client\RequestException|\Exception
     */
    public function executeWithXml(): string
    {
	    $this
		    ->setServiceType(ServiceTypes::XML)
		    ->checkServiceAvailability(ServiceTypes::XML)
		    ->prepare()
		    ->getXml();

	    $response = $this->getRequestClient()::withHeaders([
		    "Content-Type" => "text/xml;charset=utf-8"
	    ])->send('POST', $this->xml_endpoint, [
		    'body' => $this->values_to_send
	    ]);

        $response->throw();

	    $type = $response->getHeader('content-type');

	    $parsed = Header::parse($type);

	    $original_body = (string)$response->getBody();

	    $encoded_body = mb_convert_encoding($original_body, 'UTF-8', isset($parsed[0]) && $parsed[0]['Charset'] ?: 'UTF-8');

	    $this->handleNetgsmErrors($this->work_type, $encoded_body);

	    $this->handleNetgsmResponse($this->work_type, $encoded_body);

	    return $encoded_body;
    }

    protected function checkServiceAvailability(string $service_type)
    {
        if (property_exists(self::class, 'available_services')) {

            if (!in_array($service_type, $this->available_services)) {

                $message = __('This service (%service_type) is not available for this operation. Please use one of the following %available_services.');

                throw new NetgsmServiceNotAvailableException(
                    strtr($message, [
                        '%service_type' => $service_type,
                        '%available_services' => join(', ', $this->available_services)
                    ])
                );

            }

        }

        return $this;

    }

}
