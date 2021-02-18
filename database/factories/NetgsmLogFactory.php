<?php

namespace TCGunel\Netgsm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TCGunel\Netgsm\Models\NetgsmLog;
use TCGunel\Netgsm\Tests\User;
use TCGunel\Netgsm\WorkTypes;

class NetgsmLogFactory extends Factory
{
    protected $model = NetgsmLog::class;

    public function definition()
    {
        $user = User::factory()->create();

        return [
            'netgsm_loggable_id' => $user->id,
            'netgsm_loggable_type' => get_class($user),

            'work_type' => $this->faker->randomElement(array_keys(WorkTypes::readable())),
            'response_code' => $this->faker->randomElement(['00', '01', '02', '1000', '1001']),
            'response_type' => $this->faker->randomElement([0, 1]),
            'response_message' => $this->faker->realText(500),
            'payload' => (object)[
                'a' => 1,
                'b' => 2,
                'c' => 3,
                'd' => 4,
            ],
        ];
    }

}
