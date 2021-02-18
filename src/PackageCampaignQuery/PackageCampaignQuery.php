<?php

namespace TCGunel\Netgsm\PackageCampaignQuery;

use CodeDredd\Soap\Facades\Soap;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\Traits\NetgsmTrait;
use TCGunel\Netgsm\WorkTypes;

class PackageCampaignQuery extends Params
{
    use NetgsmTrait;

    /**
     * PackageCampaignQuery constructor.
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

        $this->setServiceType(config(sprintf("netgsm.%s.service", WorkTypes::PACKAGE_CAMPAIGN_QUERY)) ?? 'xml');

        $this
            ->setWorkType(WorkTypes::PACKAGE_CAMPAIGN_QUERY)
            ->setHttpEndpoint('https://api.netgsm.com.tr/balance/list/get')
            ->setSoapEndpoint('http://soap.netgsm.com.tr:8080/Sms_webservis/SMS?wsdl')
            ->setXmlEndpoint('https://api.netgsm.com.tr/balance/list/xml')
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
    protected function prepare(): PackageCampaignQuery
    {
        $this
            ->applyConfigParams($this->work_type)
            ->validateParams()
            ->formatParamsByService($this->service_type)
            ->setValuesToSend()
            ->setSoapFunction('paketkampanya')
            ->setRequestClient(null, $this->service_type);

        return $this;
    }

    public function prepareXmlData(): array
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'usercode'  => $this->values_to_send['usercode'],
                    'password'  => $this->values_to_send['password'],
                    'stip'      => $this->values_to_send['stip'],
                ],
            ]
        ];

        return $xml_array;
    }
}
