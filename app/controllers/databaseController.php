<?php

class databaseController extends baseController {

  function __construct() {
  }

  function index() {

    ob_start();
    $data = DB::select('SELECT * FROM countries LIMIT 2');
    print_r2($data);
    $select_text = ob_get_contents();
    ob_end_clean();

    //insert

    $inserted = DB::insert('INSERT INTO test (a,b) VALUES(?,?)', array(1,2) );
    $insert_text = 'inserted: ' . $inserted . '<br>';

    //update

    $affected = DB::update('UPDATE test SET b=? WHERE a=?', array(55,1) );
    $update_text = 'update affected: ' . $affected . '<br>';

    //delete

    $affected = DB::delete('DELETE test WHERE a=?', array(999) );
    $delete_text = 'delete affected: ' . $affected . '<br>';

    //show tables
    ob_start();
    $data = DB::select('SHOW TABLES');
    print_r2($data);

    $show_tables_text = ob_get_contents();
    ob_end_clean();


    //use pdo object
    //

    $pdo = DB::getPdo();

    $sql = 'SELECT * FROM language LIMIT 2';
    $query = $pdo-> query($sql);

    ob_start();
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    $pdo_text = ob_get_contents();
    ob_end_clean();

    // ---------------

    $texts = array();
    $texts['select_text'] = $select_text;
    $texts['insert_text'] = $insert_text;
    $texts['update_text'] = $update_text;
    $texts['delete_text'] = $delete_text;
    $texts['show_tables_text'] = $show_tables_text;
    $texts['pdo_text']    = $pdo_text;


    return View::make('database', $texts );
  }
}
