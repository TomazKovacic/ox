<?php

class homeController extends baseController {

  function __construct() {
  }

  function index() {


  	//$model = new homeModel();
  	//print 'Model: '; print_r2($model);  print '<br>';


  

    return View::make('index');
  }

}
