<?php namespace ox\Encryption;

use ox\Support\ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider {



	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['encrypter'] =  new Encrypter($this->app->config['key']);
	}

}
