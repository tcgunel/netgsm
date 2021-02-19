<?php

namespace TCGunel\Netgsm\Database\Factories;

use TCGunel\Netgsm\Models\NetgsmLog;
use TCGunel\Netgsm\Tests\User;
use TCGunel\Netgsm\WorkTypes;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(NetgsmLog::class, function (Faker $faker) {

    $user = factory(User::class)->create();

    return [
        'netgsm_loggable_id' => $user->id,
        'netgsm_loggable_type' => get_class($user),

        'work_type' => $faker->randomElement(array_keys(WorkTypes::readable())),
        'response_code' => $faker->randomElement(['00', '01', '02', '1000', '1001']),
        'response_type' => $faker->randomElement([0, 1]),
        'response_message' => $faker->realText(500),
        'payload' => (object)[
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ],
    ];

});
