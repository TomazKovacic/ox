<?php
namespace ox\Users;


use ox\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider {

  public function register() {
      $this->app['users'] = new Users();
  }

}
