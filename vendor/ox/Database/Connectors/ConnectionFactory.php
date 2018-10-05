<?php

namespace ox\Database\Connectors;

use PDO;
use ox\Database\MySqlConnection;
use ox\Database\SQLiteConnection;
use ox\Database\PostgresConnection;
use ox\Database\SqlServerConnection;

class ConnectionFactory {
    
    public function __construct() {}
    
    
    public function make(array $config, $name = null) {
        
        //print 'ConnectionFactory::make <br>';
        //print 'name: <u>'. $name. '</u>, config: '; print_r2($config);
        
        $config = $this->parseConfig($config, $name);
        
        return $this->createSingleConnection($config);
    }
    
    protected function parseConfig(array $config, $name) {
        
		//return array_add(array_add($config, 'prefix', ''), 'name', $name);
        
        if(!isset($config['prefix'])) { $config['prefix'] = ''; }
        if(!isset($config['name']))   { $config['name'] = $name; }
        
        return $config;
	}
    
    protected function createSingleConnection(array $config) {
        
        $pdo = $this->createConnector($config)->connect($config);
        
        //print '*PDO: '; print_r2($pdo);
        
        //return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
        return $this->createConnection($pdo, $config);
    }
    
    protected function createConnection(PDO $connection, $config) {
        
        $driver   = $config['driver'];
        $database = $config['database'];
        $prefix   = $config['prefix'];
        
		switch ($driver) {
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);

			case 'pgsql':
				return new PostgresConnection($connection, $database, $prefix, $config);

			case 'sqlite':
				return new SQLiteConnection($connection, $database, $prefix, $config);

			case 'sqlsrv':
				return new SqlServerConnection($connection, $database, $prefix, $config);
		}
    }
    
    
    
    public function createConnector(array $config) {
        
        //print 'createConnector: config'; print_r2($config);
        
		if ( ! isset($config['driver'])) {
			throw new \InvalidArgumentException("A driver must be specified.");
		}
        
		switch ($config['driver'])
		{
			case 'mysql':
				return new MySqlConnector;

			case 'pgsql':
				return new PostgresConnector;

			case 'sqlite':
				return new SQLiteConnector;

			case 'sqlsrv':
				return new SqlServerConnector;
		}

		throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
        
    }
}