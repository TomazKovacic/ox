<?php

class homeController extends baseController {

  function __construct() {
  }

  function index() {


  	//$model = new homeModel();
  	//print 'Model: '; print_r2($model);  print '<br>';


  
  	$data = DB::select('SELECT * FROM countries LIMIT 2');
  	print_r2($data);

  	//insert
  	$inserted = DB::insert('INSERT INTO test (a,b) VALUES(?,?)', array(1,2) );
	print 'inserted: ' . $inserted . '<br>';

  	//update
  	$affected = DB::update('UPDATE test SET b=? WHERE a=?', array(55,1) );
  	print 'update affected: ' . $affected . '<br>';

  	//delete
  	$affected = DB::delete('DELETE test WHERE a=?', array(999) );
  	print 'delete affected: ' . $affected . '<br>';

  	$data = DB::select('SHOW TABLES');
  	print_r2($data);

    return View::make('index', $data);
  }

}
