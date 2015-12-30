<?php
//copy database.sample.php to database.php

return array(

	/*
	|-----------------------
	|  PDO Fetch Style
	|-----------------------
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	|
	*/

	'connections' => array(

		// --- no support for now
		//'sqlite' => array(
		//	'driver'   => 'sqlite',
		//	'database' => __DIR__.'/../database/production.sqlite',
		//	'prefix'   => '',
		//),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'database',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => ''
		)

		// --- no support for now
		//'pgsql' => array(
		//	'driver'   => 'pgsql',
		//	'host'     => 'localhost',
		//	'database' => 'database',
		//	'username' => 'root',
		//	'password' => '',
		//	'charset'  => 'utf8',
		//	'prefix'   => '',
		//	'schema'   => 'public',
	  //);

	)
);
