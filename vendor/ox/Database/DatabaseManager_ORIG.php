<?php

namespace ox\Database;

//class DatabaseManager extends \PDO {
class DatabaseManager implements ConnectionResolverInterface {

  private $dbConfig = array();
  private $connections = array();
  private $defaultName;
  private $error;

  private $debug = false;

  function __construct() {


  	$app = app();
    $config = $app->getConfiguration();
    $this->dbConfig =  $config['database'];
    

    $this->defaultName = $this->dbConfig['default'];


    //connect to default db 
    $this->makeConnection($this->defaultName);

  }


  public function makeConnection($name) {

    $connections = $this->dbConfig['connections'];

    if( !isset( $connections[$name]) ) {
      throw new \InvalidArgumentException("Database [$name] not configured.");
    }

    $dbc = $this->dbConfig['connections'][$name];

    $dsn = "mysql:host={$dbc['host']};dbname={$dbc['database']};charset=utf8";
    $this->connections[$name]  = $conn = new \PDO($dsn, $dbc['username'], $dbc['password']);

    //print 'Dsn: ' . $dsn . '<br>';
    //print_r2($this->connections);

    $query = 'SET NAMES '. $dbc['charset'] . ' COLLATE '. $dbc['collation'] ;

    $stmt = $conn->prepare($query);
    $stmt->execute();

  }

  public function connection($name = null) {

    $name = $name ?: $this->getDefaultConnectionName();
    return $this->connections[$name];
  }
    
    
  public function getDefaultConnection() {
      
      //$this->dbConfig['default']
      //L return $this->app['config']['database.default'];
  }
    
  public function setDefaultConnection($name) {
      
      //L $this->app['config']['database.default'] = $name;
  }


  public function getPdo() {
    return $this->connection();
  }

  public function getDefaultConnectionName()
  {

    return  $this->defaultName;
  }


  //@see http://laravel.com/docs/4.2/database

  public function select($query, $bindings=array()) {
    
    //print 'X&gt; DB: select() <br>';
    //print '. | ' . $query . '<br>Bindings: '. implode(',', $bindings) .'<br>';

    $stmt =  $this->connection()->prepare($query);
    $stmt->execute($bindings);

    $stmt->setFetchMode(\PDO::FETCH_ASSOC);    // no constants yet

    return $stmt->fetchAll();

  }
    
  public function selectOne($query, $bindings=array()) {
      
      $records = $this->select($query, $bindings);//print $query; print_r2($bindings);print_r2($records);
      return count($records) > 0 ? reset($records) : null;
      
  }


  public function insert($query, $bindings = array()) {
    if($this->debug === true) { print 'X&gt; DB: insert() <br>'; }

    return $this->statement($query, $bindings); 
  }

  public function update($query, $bindings = array()) {
    if($this->debug === true) { print 'X&gt; DB: update() <br>'; }

    return $this->affectingStatement($query, $bindings); 
  }


  public function delete($query, $bindings = array()) {
    if($this->debug === true) { print 'X&gt; DB: delete() <br>'; }

    return $this->affectingStatement($query, $bindings);
  }

  public function statement($query, $bindings = array()) {
    //print 'X&gt; DB: statement() <br>';
    //print '. | ' . $query . ','. implode(',', $bindings) .'<br>';

    $stmt =  $this->connection()->prepare($query);
    $result = $stmt->execute($bindings); 
    //print 'statement Result: ' . $result;

  }


  public function affectingStatement($query, $bindings = array()) {
    //print 'X&gt; DB: affectingStatement() <br>';
    //
    //  print '. Q&gt; ' . $query . '<br>B&gt;'; print_r2( $bindings); print '<br>';

    $stmt =  $this->connection()->prepare($query);
    $stmt->execute($bindings);
      
    ///print 'ERX CODE: ' . $stmt->errorCode() . '<br>';
    ///print 'ERX INFO: '; print_r2($stmt->errorInfo());
      
    $this->error = $stmt->errorInfo();

    return $stmt->rowCount();
  }


  public function listen() {
    if($this->debug === true) { print 'X&gt; DB: listen() <br>'; }
    //no support
  }

  public function transaction() {
    if($this->debug === true) { print 'X&gt; DB: transaction() <br>'; }
  }

  public function beginTransaction() {
    if($this->debug === true) { print 'X&gt; DB: beginTransaction() <br>'; }
  }

  public function rollback() {
    if($this->debug === true) { print 'X&gt; DB: rollback() <br>'; }
  }

  public function commit() {
    if($this->debug === true) { print 'X&gt; DB: commit() <br>'; }
  }
    
  public function lastId() {
      return $this->connection()->lastInsertId();
  }
    
  public function error() {
      
    
      return $this->error;
  }

}//class end