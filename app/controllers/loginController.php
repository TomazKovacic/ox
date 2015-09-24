<?php

class loginController extends baseController {

  function __construct() {
  }

  function index() {

		return View::make('login');
  }

}
