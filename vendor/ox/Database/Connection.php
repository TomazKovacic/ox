<?php

namespace ox\Database;


use PDO;
use Closure;
use DateTime;

//class Connection {
//L 
class Connection implements ConnectionInterface {    
    
    protected $pdo;
    
    //protected $readPdo;
    
    protected $queryGrammar;
    
    protected $schemaGrammar;
    
    protected $postProcessor;
    
    //protected $events;
    
    protected $paginator;
    
    protected $cache;
    
    protected $fetchMode = PDO::FETCH_ASSOC;
    
    protected $transactions = 0;
    protected $queryLog = array();
    protected $loggingQueries = true;
    protected $pretending = false;
    
    protected $database;
    
    protected $tablePrefix = '';
    
    protected $config = array();
    
    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = array()) {
        
        $this->pdo = $pdo;
        
        $this->database = $database;
        
        $this->tablePrefix = $tablePrefix;
        
        $this->config = $config;
        
        $this->useDefaultQueryGrammar();
        
        $this->useDefaultPostProcessor();
        
    }
    
	public function useDefaultQueryGrammar() {
		$this->queryGrammar = $this->getDefaultQueryGrammar();
	}
    
	protected function getDefaultQueryGrammar() {
		return new Query\Grammars\Grammar;
	}
    
	public function useDefaultSchemaGrammar() {
		$this->schemaGrammar = $this->getDefaultSchemaGrammar();
	}
    
    protected function getDefaultSchemaGrammar() {}
    
	public function useDefaultPostProcessor() {
		$this->postProcessor = $this->getDefaultPostProcessor();
	}
    
	protected function getDefaultPostProcessor() {
		return new Query\Processors\Processor;
	}
    
	public function getSchemaBuilder() {
		if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); }
		return new Schema\Builder($this);
	}
    
	public function table($table) {
		$processor = $this->getPostProcessor();
		$query = new Query\Builder($this, $this->getQueryGrammar(), $processor);
		return $query->from($table);
	}
    
	public function raw($value) {
		return new Query\Expression($value);
	}
    
	public function selectOne($query, $bindings = array()) {
		$records = $this->select($query, $bindings);
		return count($records) > 0 ? reset($records) : null;
	}
    
    
    public function select($query, $bindings = array()) {
        
		return $this->run($query, $bindings, function($me, $query, $bindings)
		{
			if ($me->pretending()) return array();


			//$statement = $me->getReadPdo()->prepare($query);
			$statement = $me->getPdo()->prepare($query);

			$statement->execute($me->prepareBindings($bindings));

			return $statement->fetchAll($me->getFetchMode());
		});
        
    }
    
	public function insert($query, $bindings = array()) {
		return $this->statement($query, $bindings);
	}
    
	public function update($query, $bindings = array()) {
		return $this->affectingStatement($query, $bindings);
	}
    
	public function delete($query, $bindings = array()) {
		return $this->affectingStatement($query, $bindings);
	}
    
    
    public function statement($query, $bindings = array()) {
        
        return $this->run($query, $bindings, function($me, $query, $bindings) {
            
            //if ($me->pretending()) return true;
            
            $bindings = $me->prepareBindings($bindings);
            
            return $me->getPdo()->prepare($query)->execute($bindings);
            
        });
    }
    
    public function affectingStatement($query, $bindings = array()) {
        
        return $this->run($query, $bindings, function($me, $query, $bindings) {
            
            //if ($me->pretending()) return 0;
            
            $statement = $me->getPdo()->prepare($query);
            $statement->execute($me->prepareBindings($bindings));
            return $statement->rowCount();
        });
    }
    
	public function unprepared($query)
	{
		return $this->run($query, array(), function($me, $query, $bindings)
		{
			//if ($me->pretending()) return true;

			return (bool) $me->getPdo()->exec($query);
		});
	}
    
	public function prepareBindings(array $bindings) {
		$grammar = $this->getQueryGrammar();

		foreach ($bindings as $key => $value) {

			if ($value instanceof DateTime) {
				$bindings[$key] = $value->format($grammar->getDateFormat());
			} elseif ($value === false)
			{
				$bindings[$key] = 0;
			}
		}

		return $bindings;
	}
    
	public function transaction(Closure $callback) {
		$this->beginTransaction();

		try {
			$result = $callback($this);

			$this->commit();
            
		} catch (\Exception $e) {
			$this->rollBack();

			throw $e;
		}

		return $result;
	}
  
	public function beginTransaction() {
		++$this->transactions;

		if ($this->transactions == 1) {
			$this->pdo->beginTransaction();
		}
	}
    
	public function commit() {
        
		if ($this->transactions == 1) $this->pdo->commit();
		--$this->transactions;
	}

	public function rollBack() {
        
		if ($this->transactions == 1) {
			$this->transactions = 0;
			$this->pdo->rollBack();
		} else {
			--$this->transactions;
		}
	}
    
	public function pretend(Closure $callback) {
		$this->pretending = true;

		$this->queryLog = array();
		$callback($this);
		$this->pretending = false;

		return $this->queryLog;
	}
    
	protected function run($query, $bindings, Closure $callback) {
		$start = microtime(true);
        
		try {
			$result = $callback($this, $query, $bindings);
		}
		catch (\Exception $e) {
			throw new QueryException($query, $bindings, $e);
		}

		$time = $this->getElapsedTime($start);
		$this->logQuery($query, $bindings, $time);

		return $result;
	}
    
    public function logQuery($query, $bindings, $time = null) {
        if ( ! $this->loggingQueries) return;
        $this->queryLog[] = compact('query', 'bindings', 'time');
    }
    
	protected function getElapsedTime($start) {
		return round((microtime(true) - $start) * 1000, 2);
	}
    
	public function getPdo() {
		return $this->pdo;
	}
    
	public function setPdo(PDO $pdo) {
		$this->pdo = $pdo;
		return $this;
	}
    
	public function getName() {
        return $this->getConfig('name');
	}
    
	public function getConfig($option) {
		return array_get($this->config, $option);
	}
    
	public function getDriverName() {
		return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}
    
	public function getQueryGrammar() {
		return $this->queryGrammar;
	}
    
	public function setQueryGrammar(Query\Grammars\Grammar $grammar) {
		$this->queryGrammar = $grammar;
	}
    
	public function getSchemaGrammar()
	{
		return $this->schemaGrammar;
	}

	public function setSchemaGrammar(Schema\Grammars\Grammar $grammar)
	{
		$this->schemaGrammar = $grammar;
	}
    
	public function getPostProcessor()
	{
		return $this->postProcessor;
	}
    
	public function setPostProcessor(Processor $processor)
	{
		$this->postProcessor = $processor;
	}
    
	public function pretending()
	{
		return $this->pretending === true;
	}
    
	public function getFetchMode()
	{
		return $this->fetchMode;
	}
    
	public function setFetchMode($fetchMode)
	{
		$this->fetchMode = $fetchMode;
	}
    
	public function getQueryLog()
	{
		return $this->queryLog;
	}
    

	public function flushQueryLog()
	{
		$this->queryLog = array();
	}
    
	public function enableQueryLog()
	{
		$this->loggingQueries = true;
	}
    
	public function disableQueryLog()
	{
		$this->loggingQueries = false;
	}
    
	public function getDatabaseName()
	{
		return $this->database;
	}
    
	public function setDatabaseName($database)
	{
		$this->database = $database;
	}
    
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}
    
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;

		$this->getQueryGrammar()->setTablePrefix($prefix);
	}
    
	public function withTablePrefix(Grammar $grammar)
	{
		$grammar->setTablePrefix($this->tablePrefix);

		return $grammar;
	}
    
    
}