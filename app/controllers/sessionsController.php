<?php
class sessionsController extends baseController {

  function __construct() {
  }

  function index() {


    return View::make('sessions');
  }
}
