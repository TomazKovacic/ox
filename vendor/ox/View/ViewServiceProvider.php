<?php
namespace ox\View;


use ox\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider {

  public function register() {
      $this->app['view'] = new View();
  }

}
