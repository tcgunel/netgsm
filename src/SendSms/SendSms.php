<?php

namespace TCGunel\Netgsm\SendSms;

use CodeDredd\Soap\Facades\Soap;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\Traits\NetgsmTrait;
use TCGunel\Netgsm\WorkTypes;

class SendSms extends Params
{
    use NetgsmTrait;

    /**
     * Send Sms constructor.
     *
     * Get option set in config file, fallback to xml.
     * XML is better among others.
     * http doesn't support n:n,
     * soap may be problematic.
     *
     * @param null|Soap|Http $request_client
     */
    public function __construct($request_client = null)
    {
        parent::__construct();

        $this->setServiceType(config(sprintf("netgsm.%s.service", WorkTypes::SEND_SMS)) ?? 'xml');

        $this
            ->setWorkType(WorkTypes::SEND_SMS)
            ->setHttpEndpoint('https://api.netgsm.com.tr/sms/send/get/')
            ->setSoapEndpoint('http://soap.netgsm.com.tr:8080/Sms_webservis/SMS?wsdl')
            ->setXmlEndpoint('https://api.netgsm.com.tr/sms/send/xml')
            ->setRequestClient($request_client);
    }

    /**
     * General preparation before making api request.
     *
     * 1-) Get options from config file and apply to class variables.
     * 2-) Check if any required field is not present.
     * 3-) Format params for different service types, eg. urlencode password when using http GET request.
     * 4-) Create a key value array matches with Netgsm api keys & values,
     *     every service has different key names for same method for some reason?
     *
     * @return $this
     * @throws \Exception
     */
    protected function prepare(): SendSms
    {
        $this
            ->applyConfigParams($this->work_type)
            ->validateParams()
            ->formatParamsByService($this->service_type)
            ->setValuesToSend()
            ->setSoapFunction(is_array($this->msg) ? 'smsGonderNNV2' : 'smsGonder1NV2')
            ->setRequestClient(null, $this->service_type);

        return $this;
    }

    public function prepareXmlData(): array
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'company'   => ['value' => 'Netgsm'],
                    'usercode'  => $this->values_to_send['usercode'],
                    'password'  => $this->values_to_send['password'],
                    'type'      => $this->values_to_send['type'],
                    'msgheader' => $this->values_to_send['msgheader'],
                ],
                'body'   => [],
            ]
        ];

        if ($this->encoding) {

            $xml_array['mainbody']['header']['company']['attr'] = ['dil' => 'TR'];

        }

        if ($this->values_to_send['type'] === '1:n') {

            $xml_array['mainbody']['body'] = [
                'msg' => ['cdata' => true, 'value' => $this->values_to_send['message']],
                'no'  => [],
            ];

            if (is_array($this->values_to_send['no'])) {

                $xml_array['mainbody']['body']['no']['values'] = $this->values_to_send['no'];

            } else {

                $xml_array['mainbody']['body']['no'] = $this->values_to_send['no'];

            }

        } else {

            $xml_array['mainbody']['body']['mp'] = ['values' => []];

            foreach ($this->values_to_send['message'] as $k => $msg) {

                $xml_array['mainbody']['body']['mp']['values'][] = [
                    'msg' => ['cdata' => true, 'value' => $msg],
                    'no'  => $this->values_to_send['no'][$k],
                ];

            }

        }

        return $xml_array;
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
}
