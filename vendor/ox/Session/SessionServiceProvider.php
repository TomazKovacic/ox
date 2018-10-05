<?php namespace ox\Session;

use ox\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider {

  public function register() {

    $this->app['session'] = new SessionManager($this->app );
  }

}
