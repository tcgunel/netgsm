<?php

namespace TCGunel\Netgsm\SendOtpSms;

use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\ServiceTypes;
use TCGunel\Netgsm\Traits\NetgsmTrait;
use TCGunel\Netgsm\WorkTypes;

class SendOtpSms extends Params
{
    use NetgsmTrait;

    public $available_services = [
        ServiceTypes::XML,
        ServiceTypes::HTTP,
    ];

    /**
     * Send Sms constructor.
     *
     * Get option set in config file, fallback to xml.
     * XML is better among others.
     * http doesn't support n:n.
     *
     * @param null|Http $request_client
     */
    public function __construct($request_client = null)
    {
        parent::__construct();

        $this->setServiceType(config(sprintf("netgsm.%s.service", WorkTypes::SEND_SMS)) ?? 'xml');

        $this
            ->setWorkType(WorkTypes::SEND_OTP_SMS)
            ->setHttpEndpoint('https://api.netgsm.com.tr/sms/send/otp')
            ->setXmlEndpoint('https://api.netgsm.com.tr/sms/send/otp')
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
    protected function prepare(): SendOtpSms
    {
        $this
            ->applyConfigParams($this->work_type)
            ->validateParams()
            ->formatParamsByService($this->service_type)
            ->setValuesToSend()
            ->setRequestClient(null, $this->service_type);

        return $this;
    }

    public function prepareXmlData(): array
    {
        return [
            'mainbody' => [
                'header' => [
                    'usercode'  => $this->values_to_send['usercode'],
                    'password'  => $this->values_to_send['password'],
                    'msgheader' => $this->values_to_send['msgheader'],
                ],
                'body'   => [
                    'msg' => ['cdata' => true, 'value' => $this->values_to_send['message']],
                    'no'  => $this->values_to_send['no'],
                ],
            ]
        ];
    }
}
