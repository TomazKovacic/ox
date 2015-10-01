<?php

class loginController extends baseController {

  function __construct() {
  }

  function index() {

		return View::make('login');
  }


  function process() {

  	print 'loginController::process()';
  	exit();
  }

}
