<?php

class aboutController extends baseController {
//class aboutController {

  function __construct() {

  	//print 'aboutController::__construct <br>';
  }

  function index() {

  	//return 'aboutController::index() return';

    return View::make('about');
  }
  
  function about() {

  	//return 'aboutController::index() return';

    return View::make('about');
  }
}
