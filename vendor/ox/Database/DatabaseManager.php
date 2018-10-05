<?php

namespace ox\Database;

use ox\Database\Connectors\ConnectionFactory;

class DatabaseManager implements ConnectionResolverInterface {

  private $dbConfig = array();
  private $connections = array();
  private $defaultName;
  private $error;

  private $debug = false;

  function __construct() {

    $config = app()->getConfiguration();
    $this->dbConfig  =  $config['database'];
  }


  public function makeConnection($name) {

    $connections = $this->dbConfig['connections'];

    if( !isset( $connections[$name]) ) {
      throw new \InvalidArgumentException("Database [$name] not configured.");
    }

    $dbc = $this->dbConfig['connections'][$name];
      
    //print 'dbc:'; print_r2($dbc);

    //$dsn = "mysql:host={$dbc['host']};dbname={$dbc['database']};charset=utf8";
    //$this->connections[$name]  = $conn = new \PDO($dsn, $dbc['username'], $dbc['password']);

    //print 'Dsn: ' . $dsn . '<br>';
    //print_r2($this->connections);

    //PREPARE $query = 'SET NAMES '. $dbc['charset'] . ' COLLATE '. $dbc['collation'] ;

    //PREPARE $stmt = $conn->prepare($query);
    //PREPARE $stmt->execute();
      
      
    //print 'DBM: makeConnection <br>';
    //print 'name: ' . $name . '<br><br>';
    //print 'db config: '; print_r2($dbc['driver']);
      
    $driver = $this->dbConfig['connections'][$name];
    $factory = new ConnectionFactory($name, $driver);
    

    return $c = $factory->make($dbc, $name);
      
      
    //print 'CCC :'; print_r2($c);
      
    

      
    
    return $this->factory->make($config, $name);

  }
    
 protected function prepare(Connection $connection) {
     
     //L $connection->setFetchMode($this->app['config']['database.fetch']);
     
    $connection->setFetchMode( $this->dbConfig['fetch'] );
         
    //print 'Prepare EXIT: '; exit();
     
    return $connection;
 }

  public function connection($name = null) {

    //$name = $name ?: $this->getDefaultConnectionName();
    //print 'DatabaseManager::connection() <br>';
      
    $name = $name ?: $this->getDefaultConnection();
      
    if ( ! isset($this->connections[$name])) {
        
        $connection = $this->makeConnection($name);
        $this->connections[$name] = $this->prepare($connection);
    }
    return $this->connections[$name];
  }
    
    
  public function getDefaultConnection() {
            
      $config = app()->getConfiguration();     
      return $config['database']['default'];
  }
    
  public function setDefaultConnection($name) {
      
      //L $this->app['config']['database.default'] = $name;
  }

    /**
    *
    * Podej metodo konekciji
    * DB::select -> $this->connection()->select
    */
    
	public function __call($method, $parameters)
	{
		//x: print 'DatabaseManager::__call() <br>';
		//x: print '. | ' . get_class( $this->connection() ). ' <br>';
		//x: print '. | method: '. $method .' , parameters: '. implode(',', $parameters) .' <br>';
		
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}//class end