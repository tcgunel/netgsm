<?php

namespace TCGunel\Netgsm\Tests;

use TCGunel\Netgsm\NetgsmServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create("tr_TR");

        // additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            NetgsmServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // import the CreatePostsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_netgsm_logs_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';

        // run the up() method of that migration class
        (new \CreateNetgsmLogsTable)->up();
        (new \CreateUsersTable)->up();
    }


}
