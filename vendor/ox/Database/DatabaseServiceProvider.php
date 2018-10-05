<?php namespace ox\Database;

use ox\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {

		$this->app['DB'] =  new DatabaseManager( $this->app->config['database'] );
        
        
        //on boot, no boot implementet yet
        
        // \ox\Model\Model::setConnectionResolver($this->app['db']);
        \ox\Model\Model::setConnectionResolver( $this->app['DB'] );
	}

}
