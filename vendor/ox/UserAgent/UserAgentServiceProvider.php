<?php

namespace ox\UserAgent;


use ox\Support\ServiceProvider;

class UserAgentServiceProvider extends ServiceProvider {

  public function register() {
      $this->app['userAgent'] = new UserAgent( $_SERVER['HTTP_USER_AGENT'] );
  }

}
