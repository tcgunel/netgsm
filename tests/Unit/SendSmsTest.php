<?php

namespace TCGunel\Netgsm\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\Exceptions\NetgsmRequiredFieldsException;
use TCGunel\Netgsm\Exceptions\NetgsmSendSmsException;
use TCGunel\Netgsm\SendSms\SendSms;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Tests\TestCase;
use TCGunel\Netgsm\Traits\ErrorsTrait;

class SendSmsTest extends TestCase
{
    use RefreshDatabase, ErrorsTrait;

    public $username;

    public $password;

    public $message;

    public $messages;

    public $receiver;

    public $receivers;

    public $successful_return_code;

    public $successful_return_id;

    public function setUp(): void
    {
        parent::setUp();

        $amount = rand(1, 3);

        $this->username = htmlspecialchars($this->faker->userName);

        $this->password = htmlspecialchars($this->faker->password);

        $this->message = $this->faker->realText(140);

        $this->messages = array_map(function () {

            return $this->faker->realText(140);;

        }, range(0, $amount));

        $this->receiver = $this->faker->e164PhoneNumber;

        $this->receivers = array_map(function () {

            return $this->faker->e164PhoneNumber;

        }, range(0, $amount));

        $this->successful_return_code = $this->faker->randomElement(['00', '01', '02']);

        $this->successful_return_id = $this->faker->randomNumber(9);

    }

    function test_can_set_username()
    {
        $sendSms = new SendSms();

        $sendSms->setUsername($this->username);

        $this->assertEquals($this->username, $sendSms->getUsername());
    }

    function test_can_set_password()
    {
        $sendSms = new SendSms();

        $sendSms->setPassword($this->password);

        $this->assertEquals($this->password, $sendSms->getPassword());
    }

    function test_can_execute_with_http_1_message_to_1_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendSms->executeWithHttp();

        $this->assertEquals($this->successful_return_code, $sendSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendSms->result);
    }

    function test_can_execute_with_http_1_message_to_n_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->message);

        $sendSms->executeWithHttp();

        $this->assertEquals($this->successful_return_code, $sendSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendSms->result);
    }

    function test_can_execute_with_http_n_message_to_n_receiver_throws_exception()
    {
        $this->expectException(NetgsmSendSmsException::class);

        $http_client = Http::fake(function ($request) {

            return Http::response();

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->messages);

        $sendSms->executeWithHttp();
    }

    function test_can_execute_with_http_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getSendSmsErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {
            return Http::response($netgsm_error_code);
        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->message);

        $sendSms->executeWithHttp();
    }

    function test_can_execute_with_http_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request) {
            return Http::response();
        });

        $sendSms = new SendSms($http_client);

        $sendSms->executeWithHttp();
    }

    function test_can_execute_with_xml_1_message_to_1_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendSms->executeWithXml();

        $this->assertEquals($this->successful_return_code, $sendSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendSms->result);
    }

    function test_can_execute_with_xml_1_message_to_n_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->message);

        $sendSms->executeWithXml();

        $this->assertEquals($this->successful_return_code, $sendSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendSms->result);
    }

    function test_can_execute_with_xml_n_message_to_n_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->messages);

        $sendSms->executeWithXml();

        $this->assertEquals($this->successful_return_code, $sendSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendSms->result);
    }

    function test_can_execute_with_xml_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getSendSmsErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {

            return Http::response($netgsm_error_code);

        });

        $sendSms = new SendSms($http_client);

        $sendSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receivers)->setMsg($this->messages);

        $sendSms->executeWithXml();
    }

    function test_can_execute_with_xml_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request) {
            return Http::response();
        });

        $sendSms = new SendSms($http_client);

        $sendSms->executeWithXml();
    }

    function test_can_prepare_xml_from_array_for_1_message_to_1_receiver()
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'company' => ['value' => 'Netgsm', 'attr' => ['dil' => 'TR']],
                    'usercode' => 'usercode',
                    'password' => 'password',
                    'type' => '1:n',
                    'msgheader' => 'msgheader',
                ],
                'body' => [
                    'msg' => 'msg',
                    'no' => 'no',
                ]
            ]
        ];

        $sendSms = new SendSms();

        $xml_result = $sendSms->outputXml($xml_array);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Xml/SendSms1MessageTo1ReceiverTest.xml', $xml_result);
    }

    function test_can_prepare_xml_from_array_for_1_message_to_n_receiver()
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'company' => ['value' => 'Netgsm', 'attr' => ['dil' => 'TR']],
                    'usercode' => 'usercode',
                    'password' => 'password',
                    'type' => '1:n',
                    'msgheader' => 'msgheader',
                ],
                'body' => [
                    'msg' => 'msg',
                    'no' => ['values' => ['no', 'no', 'no']],
                ]
            ]
        ];

        $sendSms = new SendSms();

        $xml_result = $sendSms->outputXml($xml_array);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Xml/SendSms1MessageToNReceiverTest.xml', $xml_result);
    }

    function test_can_prepare_xml_from_array_for_n_message_to_n_receiver()
    {
        $xml_array = [
            'mainbody' => [
                'header' => [
                    'company' => ['value' => 'Netgsm', 'attr' => ['dil' => 'TR']],
                    'usercode' => 'usercode',
                    'password' => 'password',
                    'type' => 'n:n',
                    'msgheader' => 'msgheader',
                ],
                'body' => [
                    'mp' => [
                        'values' => [
                            [
                                'msg' => 'msg',
                                'no' => 'no',
                            ],
                            [
                                'msg' => 'msg',
                                'no' => 'no',
                            ],
                            [
                                'msg' => 'msg',
                                'no' => 'no',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $sendSms = new SendSms();

        $xml_result = $sendSms->outputXml($xml_array);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../Xml/SendSmsNMessageToNReceiverTest.xml', $xml_result);
    }
}
