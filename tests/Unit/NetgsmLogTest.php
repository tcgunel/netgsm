<?php

namespace TCGunel\Netgsm\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use TCGunel\Netgsm\Models\NetgsmLog;
use TCGunel\Netgsm\Tests\TestCase;
use TCGunel\Netgsm\Tests\User;
use TCGunel\Netgsm\WorkTypes;

class NetgsmLogTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();


    }

    function test_a_netgsm_log_has_a_netgsm_loggable_id()
    {
        $netgsm_loggable_id = $this->faker->numberBetween(999, 999999);

        $netgsm_log = NetgsmLog::factory()->create([
            'netgsm_loggable_id' => $netgsm_loggable_id,
        ]);

        $this->assertEquals($netgsm_loggable_id, $netgsm_log->netgsm_loggable_id);
    }

    function test_a_netgsm_log_has_a_netgsm_loggable_type()
    {
        $netgsm_log = NetgsmLog::factory()->create(['netgsm_loggable_type' => User::class]);

        $this->assertEquals(User::class, $netgsm_log->netgsm_loggable_type);
    }

    function test_a_netgsm_log_belongs_to_another_model()
    {
        /** @var User $another_model */
        $another_model = User::factory()->create();

        $data = [
            'work_types' => array_keys(WorkTypes::readable()),
            'response_codes' => ['00', '01', '02', '1000', '1001'],
            'response_types' => ['0', '1'],
            'response_message' => $this->faker->realText(500),
            'payload' => (object)[
                'a' => 1,
                'b' => 2,
                'c' => 3,
                'd' => 4,
            ],
        ];


        $another_model->netgsm_logs()->create([
            'work_type' => $this->faker->randomElement($data['work_types']),
            'response_code' => $this->faker->randomElement($data['response_codes']),
            'response_type' => $this->faker->randomElement($data['response_types']),
            'response_message' => $data['response_message'],
            'payload' => $data['payload'],
        ]);

        $this->assertCount(1, NetgsmLog::all());
        $this->assertCount(1, $another_model->netgsm_logs);

        tap($another_model->netgsm_logs()->first(), function (NetgsmLog $netgsm_log) use ($data, $another_model) {
            $this->assertContains($netgsm_log->work_type, $data['work_types']);
            $this->assertContains($netgsm_log->response_code, $data['response_codes']);
            $this->assertContains($netgsm_log->response_type, $data['response_types']);
            $this->assertEquals($netgsm_log->response_message, $data['response_message']);
            $this->assertEquals($netgsm_log->payload, $data['payload']);
            $this->assertTrue($netgsm_log->netgsm_loggable()->is($another_model));
        });

    }

    function test_a_netgsm_log_has_a_work_type()
    {
        $work_type = $this->faker->randomElement(array_keys(WorkTypes::readable()));

        $netgsm_log = NetgsmLog::factory()->create([
            'work_type' => $work_type,
        ]);

        $this->assertEquals($work_type, $netgsm_log->work_type);
    }

    function test_a_netgsm_log_has_a_response_code()
    {
        $response_code = $this->faker->randomElement(['00', '01', '02', '1000', '1001']);

        $netgsm_log = NetgsmLog::factory()->create([
            'response_code' => $response_code,
        ]);

        $this->assertEquals($response_code, $netgsm_log->response_code);
    }

    function test_a_netgsm_log_has_a_response_type()
    {
        $response_type = $this->faker->randomElement([0, 1]);

        $netgsm_log = NetgsmLog::factory()->create([
            'response_type' => $response_type,
        ]);

        $this->assertEquals($response_type, $netgsm_log->response_type);
    }

    function test_a_netgsm_log_has_a_response_message()
    {
        $response_message = $this->faker->realText(500);

        $netgsm_log = NetgsmLog::factory()->create([
            'response_message' => $response_message,
        ]);

        $this->assertEquals($response_message, $netgsm_log->response_message);
    }

    function test_a_netgsm_log_has_a_payload()
    {
        $payload = (object)[
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];

        $netgsm_log = NetgsmLog::factory()->create([
            'payload' => $payload,
        ]);

        $this->assertEquals($payload, $netgsm_log->payload);
    }
}
