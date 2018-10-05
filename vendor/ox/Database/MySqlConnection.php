<?php

namespace ox\Database;

use ox\Database\Schema\MySqlBuilder;

class MySqlConnection extends Connection {
    
    
    public function lastId() {
        
        $last_id = \DB::selectOne('SELECT LAST_INSERT_ID() as last_id');
        //print_r2($last_id);
        //$last_id = array()$last_id;
        return $last_id->last_id;
    }
    
}