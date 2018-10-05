<?php
namespace ox\Routing;


use ox\Support\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider {

  public function register() {
      $this->app['router'] = new Router;
      $this->app['redirect'] = new Redirector;
  }

}
