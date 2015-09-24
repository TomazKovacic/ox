<?php

class homeController extends baseController {

  function __construct() {
  }

  function index() {

    return View::make('index');
  }

}
