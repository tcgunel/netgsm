<?php

namespace TCGunel\Netgsm\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\Exceptions\NetgsmRequiredFieldsException;
use TCGunel\Netgsm\PackageCampaignQuery\PackageCampaignQuery;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Tests\TestCase;
use TCGunel\Netgsm\Traits\ErrorsTrait;

class PackageCampaignQueryTest extends TestCase
{
    use RefreshDatabase, ErrorsTrait;

    public $username;

    public $password;

    public function setUp(): void
    {
        parent::setUp();

        $this->username = htmlspecialchars($this->faker->userName);

        $this->password = htmlspecialchars($this->faker->password);

    }

    function test_can_set_username()
    {
        $packageCampaignQuery = new PackageCampaignQuery();

        $packageCampaignQuery->setUsername($this->username);

        $this->assertEquals($this->username, $packageCampaignQuery->getUsername());
    }

    function test_can_set_password()
    {
        $packageCampaignQuery = new PackageCampaignQuery();

        $packageCampaignQuery->setPassword($this->password);

        $this->assertEquals($this->password, $packageCampaignQuery->getPassword());
    }

    function test_can_execute_with_http()
    {
        $expected = [
            ['1000', 'Adet Flash Sms'],
            ['953', 'Adet OTP Sms'],
            ['643', 'Adet', 'SMS'],
        ];

        $http_client = Http::fake(function ($request) {
            return Http::response('1000 | Adet Flash Sms | <BR>953 | Adet OTP Sms | <BR>643 | Adet | SMS<BR>');
        });

        $packageCampaignQuery = new PackageCampaignQuery($http_client);

        $packageCampaignQuery->setPassword($this->password)->setUsername($this->username);

        $packageCampaignQuery->executeWithHttp();

        $this->assertEquals($expected, $packageCampaignQuery->result);
    }

    function test_can_execute_with_http_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getPackageCampaignQueryErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {
            return Http::response($netgsm_error_code);
        });

        $packageCampaignQuery = new PackageCampaignQuery($http_client);

        $packageCampaignQuery->setPassword($this->password)->setUsername($this->username);

        $packageCampaignQuery->executeWithHttp();
    }

    function test_can_execute_with_http_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request)  {
            return Http::response();
        });

        $creditQuery = new PackageCampaignQuery($http_client);

        $creditQuery->executeWithHttp();
    }

    function test_can_execute_with_xml()
    {
        $expected = [
            ['1000', 'Adet Flash Sms'],
            ['953', 'Adet OTP Sms'],
            ['643', 'Adet', 'SMS'],
        ];

        $http_client = Http::fake(function ($request) {
            return Http::response("1000 | Adet Flash Sms | <BR>953 | Adet OTP Sms | <BR>643 | Adet | SMS<BR>");
        });

        $packageCampaignQuery = new PackageCampaignQuery($http_client);

        $packageCampaignQuery->setPassword($this->password)->setUsername($this->username);

        $packageCampaignQuery->executeWithXml();

        $this->assertEquals($expected, $packageCampaignQuery->result);
    }

    function test_can_execute_with_xml_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getPackageCampaignQueryErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {
            return Http::response($netgsm_error_code);
        });

        $packageCampaignQuery = new PackageCampaignQuery($http_client);

        $packageCampaignQuery->setPassword($this->password)->setUsername($this->username);

        $packageCampaignQuery->executeWithXml();
    }

    function test_can_execute_with_xml_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request)  {
            return Http::response();
        });

        $creditQuery = new PackageCampaignQuery($http_client);

        $creditQuery->executeWithXml();
    }

    function test_can_prepare_xml_from_array()
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'usercode' => 'usercode',
                    'password' => 'password',
                    'stip' => 'stip',
                ],
            ]
        ];

        $packageCampaignQuery = new PackageCampaignQuery();

        $xml_result = $packageCampaignQuery->outputXml($xml_array);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Xml/PackageCampaignQueryTest.xml', $xml_result);
    }
}
