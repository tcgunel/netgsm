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