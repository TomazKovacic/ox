<?php

namespace ox\Database\Connectors;

//L class MySqlConnector extends Connector implements ConnectorInterface

class MySqlConnector extends Connector {
    
    
	/**
     * LAR
	 * Establish a database connection.
	 *
	 * @param  array  $config
	 * @return \PDO
	 */
	public function connect(array $config) {
        
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);
        
        
        $connection = $this->createConnection($dsn, $config, $options);
        
        $collation = $config['collation'];
        $charset = $config['charset'];
        
		$names = "set names '$charset' collate '$collation'";

		$connection->prepare($names)->execute();
        
        
		if (isset($config['strict']) && $config['strict'])
		{
			$connection->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
		}
        
        
        return $connection;
    }
    
    protected function getDsn(array $config) {
        
        //uukill();//exit(__FILE__ .':'. __LINE__);
        
		extract($config);

		$dsn = "mysql:host={$host};dbname={$database}";

		if (isset($config['port'])) {
			$dsn .= ";port={$port}";
		}
        
        return $dsn;
    }
}