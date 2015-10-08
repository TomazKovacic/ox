<?php

class routesController extends baseController {

  function __construct() {

  	$this->title = 'Routes';
  }

  function index() {

  	//return 'aboutController::index() return';

  	$data = array('title' => $this->title);
    return View::make('routes', $data);
  }

  function single_page($id) {

  	$data = array('title' => $this->title, 'id'=> $id);
  	return View::make('routes', $data);
  }

  function named_page($name) {

  	$data = array('title' => $this->title, 'name'=> $name);
  	return View::make('routes', $data);	
  }

  function third_page($name, $id) {

  	$data = array('title' => $this->title, 'id'=> $id, 'name'=> $name);
  	return View::make('routes', $data);	
  }
}
