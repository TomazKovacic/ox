<?php

class logoutController extends baseController {

  function __construct() {
  }

  function index() {

    //return View::make('logoutView');
    //return 'logoutController::index() return';

  	Auth::Logout();

    return Redirect::to('/user');
  }

}
