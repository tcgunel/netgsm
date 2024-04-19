<?php

namespace TCGunel\Netgsm\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use TCGunel\Netgsm\Exceptions\NetgsmRequiredFieldsException;
use TCGunel\Netgsm\Exceptions\NetgsmServiceNotAvailableException;
use TCGunel\Netgsm\SendOtpSms\SendOtpSms;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Tests\TestCase;
use TCGunel\Netgsm\Traits\ErrorsTrait;

class SendOtpSmsTest extends TestCase
{
    use RefreshDatabase, ErrorsTrait;

    public $username;

    public $password;

    public $message;

    public $receiver;

    public $successful_return_code;

    public $successful_return_id;

    public function setUp(): void
    {
        parent::setUp();

        $this->username = htmlspecialchars($this->faker->userName);

        $this->password = htmlspecialchars($this->faker->password);

        $this->message = $this->faker->realText(140);

        $this->receiver = $this->faker->e164PhoneNumber;

        $this->successful_return_code = $this->faker->randomElement(['00']);

        $this->successful_return_id = $this->faker->randomNumber(9);

    }

    function test_can_set_username()
    {
        $sendOtpSms = new SendOtpSms();

        $sendOtpSms->setUsername($this->username);

        $this->assertEquals($this->username, $sendOtpSms->getUsername());
    }

    function test_can_set_password()
    {
        $sendOtpSms = new SendOtpSms();

        $sendOtpSms->setPassword($this->password);

        $this->assertEquals($this->password, $sendOtpSms->getPassword());
    }

    function test_can_execute_with_http_1_message_to_1_receiver()
    {
        $response = $this->successful_return_code . ' ' . $this->successful_return_id;

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendOtpSms->executeWithHttp();

        $this->assertEquals($this->successful_return_code, $sendOtpSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendOtpSms->result);
    }

    function test_can_execute_with_http_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getSendOtpSmsErrors()));

        $http_client = Http::fake(function ($request) use ($netgsm_error_code) {

            return Http::response($netgsm_error_code);

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendOtpSms->executeWithHttp();
    }

    function test_can_execute_with_http_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request) {

            return Http::response();

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->executeWithHttp();
    }

    function test_can_execute_with_xml_1_message_to_1_receiver()
    {
        $response = sprintf(
            '<?xml version="1.0"?><xml><main><code>%s</code><jobID>%s</jobID></main></xml>',
            $this->successful_return_code,
            $this->successful_return_id
        );

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendOtpSms->executeWithXml();

        $this->assertEquals($this->successful_return_code, $sendOtpSms->result_code);
        $this->assertEquals($this->successful_return_id, $sendOtpSms->result);
    }

    function test_can_execute_with_xml_throws_netgsm_exception()
    {
        $this->expectException(NetgsmException::class);

        $netgsm_error_code = $this->faker->randomElement(array_keys(self::getSendOtpSmsErrors()));

        $response = sprintf(
            '<?xml version="1.0"?><xml><main><code>%s</code><error>%s</error></main></xml>',
            $netgsm_error_code,
            self::getSendOtpSmsErrors()[$netgsm_error_code]
        );

        $http_client = Http::fake(function ($request) use ($response) {

            return Http::response($response);

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->setPassword($this->password)->setUsername($this->username)->setGsm($this->receiver)->setMsg($this->message);

        $sendOtpSms->executeWithXml();
    }

    function test_can_execute_with_xml_throws_required_fields_exception()
    {
        $this->expectException(NetgsmRequiredFieldsException::class);

        $http_client = Http::fake(function ($request) {

            return Http::response();

        });

        $sendOtpSms = new SendOtpSms($http_client);

        $sendOtpSms->executeWithXml();
    }
}
