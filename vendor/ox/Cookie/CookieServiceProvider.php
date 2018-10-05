<?php namespace ox\Cookie;

use ox\Support\ServiceProvider;

class CookieServiceProvider extends ServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {

    $config = $this->app->config['session'];
    $cookie_jar = new CookieJar();
    $cookie_jar-> setDefaultPathAndDomain($config['path'], $config['domain']);
		$this->app['cookie'] = $cookie_jar;
	}

}
