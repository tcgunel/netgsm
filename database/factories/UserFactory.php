<?php
namespace TCGunel\Netgsm\Database\Factories;

use Illuminate\Support\Str;
use TCGunel\Netgsm\Tests\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'remember_token' => Str::random(10),
    ];

});
