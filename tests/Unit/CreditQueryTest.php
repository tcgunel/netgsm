<?php

namespace TCGunel\Netgsm\Tests\Unit;

use CodeDredd\Soap\Facades\Soap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\CreditQuery\CreditQuery;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Exceptions\NetgsmRequiredFieldsException;
use TCGunel\Netgsm\Models\NetgsmLog;
use TCGunel\Netgsm\Tests\TestCase;
use TCGunel\Netgsm\Traits\ErrorsTrait;

class CreditQueryTest extends TestCase
{
    use RefreshDatabase, ErrorsTrait;

    public $username;

    public $password;

    public $soap_function_name = 'kredi';

    public function setUp(): void
    {
        parent::setUp();

        $this->username = htmlspecialchars($this->faker->userName);

        $this->password = htmlspecialchars($this->faker->password);

    }

    function test_can_set_username()
    {
        $creditQuery = new CreditQuery();

        $creditQuery->setUsername($this->username);

        $this->assertEquals($this->username, $creditQuery->getUsername());
    }

    function test_can_set_password()
    {
        $creditQuery = new CreditQuery();

        $creditQuery->setPassword($this->password);

        $this->assertEquals($this->password, $creditQuery->getPassword());
    }

    function test_can_execute_with_http_and_logs()
    {
        $http_client = Http::fake(function ($request) {
            return Http::response('00 0,750');
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithHttp();

        $this->assertEquals('00', $creditQuery->result_code);
        $this->assertEquals('0,750', $creditQuery->result);

        tap(NetgsmLog::all()->first(), function (NetgsmLog $netgsm_log) {
            $this->assertEquals('00', $netgsm_log->response_code);
            $this->assertEquals('0,750', $netgsm_log->response_message);
        });
    }

    function test_can_execute_with_http_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getCreditQueryErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {
            return Http::response($netgsm_error_code);
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithHttp();
    }

    function test_can_execute_with_http_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request)  {
            return Http::response();
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->executeWithHttp();
    }

    function test_can_execute_with_xml()
    {
        $http_client = Http::fake(function ($request) {
            return Http::response('00 0,750');
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithXml();

        $this->assertEquals('00', $creditQuery->result_code);
        $this->assertEquals('0,750', $creditQuery->result);
    }

    function test_can_execute_with_xml_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getCreditQueryErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {
            return Http::response($netgsm_error_code);
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithXml();
    }

    function test_can_execute_with_xml_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request)  {
            return Http::response();
        });

        $creditQuery = new CreditQuery($http_client);

        $creditQuery->executeWithXml();
    }

    function test_can_execute_with_soap()
    {
        $client = Soap::fake([
            $this->soap_function_name => Soap::response(['return' => '0,750'], 200, ['Headers']),
        ]);

        $creditQuery = new CreditQuery($client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithSoap();

        $this->assertEquals('0,750', $creditQuery->result);
    }

    function test_can_execute_with_soap_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getCreditQueryErrors()));

        $client = Soap::fake([
            $this->soap_function_name => Soap::response(['return' => $netgsm_error_code], 200, ['Headers']),
        ]);

        $creditQuery = new CreditQuery($client);

        $creditQuery->setPassword($this->password)->setUsername($this->username);

        $creditQuery->executeWithSoap();
    }

    function test_can_execute_with_soap_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $client = Soap::fake([
            $this->soap_function_name => Soap::response(['return' => null], 422, ['Headers']),
        ]);

        $creditQuery = new CreditQuery($client);

        $creditQuery->executeWithSoap();
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

        $creditQuery = new CreditQuery();

        $xml_result = $creditQuery->outputXml($xml_array);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Xml/CreditQueryTest.xml', $xml_result);
    }
}
