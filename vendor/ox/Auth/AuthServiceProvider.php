<?php namespace ox\Auth;

use ox\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {

		$this->app['Auth'] =  new AuthManager();
	}

}
