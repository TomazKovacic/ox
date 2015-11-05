<?php

class userController extends baseController {


  function __construct() {

  	//print 'userController::__construct <br>';
  }

  function index() {

  	//return 'userController::index() return';

    return View::make('user');
  }
}
