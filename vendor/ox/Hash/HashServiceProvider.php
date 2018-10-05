<?php namespace ox\Hash;

use ox\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider {



	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['hash'] =  new Hash();  //needed? $this->app->config['key']
	}

}
