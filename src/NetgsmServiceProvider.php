<?php

namespace TCGunel\Netgsm;

use Illuminate\Support\ServiceProvider;

class NetgsmServiceProvider extends ServiceProvider
{
    /**
     * Publishes configuration file.
     *
     * @return  void
     */
    public function boot()
    {
        $this->publishes([

            __DIR__ . '/../config/netgsm.php' => config_path('netgsm.php'),

        ], 'netgsm-config');

        if ($this->app->runningInConsole()) {

            if (!class_exists('CreateNetgsmLogsTable')) {

                $this->publishes([
                    __DIR__ . '/../database/migrations/create_netgsm_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_netgsm_logs_table.php'),
                ], 'migrations');

            }

        }
    }

    /**
     * Make config publishment optional by merging the config from the package.
     *
     * @return  void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/netgsm.php',
            'netgsm'
        );
    }
}
