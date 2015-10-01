<?php
	namespace ox\Facades;

	class Facade {
		
		protected static $app;
		protected static $instance;
		
		public static function init() {	
			//print '<br><br>****  Facade::init (static)  **** <br><br>';

			static::$instance = array();
		}
		
		
		public static function clear() {  //alias
			static::init();
		}

		public static function clearInstance($name) {
			unset(static::$instance[$name]);
		}
	
		public static function dump() {
			
			print '<hr>DUMP: <pre>';
			print_r ( static::$app );
			print '</pre><hr>';
		}
		
		public static function setApplication($app) {
			//print '<br><br>****  Facade::setApplication (static)  **** <br><br>';
			static::$app = $app;
		}

		public static function getApplication() {
			//print '<br><br>****  Facade::getApplication (static)  **** <br><br>';
			return static::$app;
		}
		
		public static function __callStatic($method, $args) {

			$debug = false;
			//$debug = true;


			$name = static::getAccessor();
			$instance = static::$app->bindings[$name];


			//print_r2( static::$app );
			//print_r2( static::$app->bindings );


			if( $debug == true ) {

				print '****  calling Facade::__callStatic ...  **** <br>';
				print ' --- instance name: '. $name .'<br>';
				print ' --- method: '. $method .'<br>';
				print ' --- args: ';
				print ' [ ' . @implode(', ', $args) . ' ]<br><br>';
			}


			switch (count($args))
			{
				case 0:
					return $instance->$method();

				case 1:
					return $instance->$method($args[0]);

				case 2:
					return $instance->$method($args[0], $args[1]);

				case 3:
					return $instance->$method($args[0], $args[1], $args[2]);

				case 4:
					return $instance->$method($args[0], $args[1], $args[2], $args[3]);

				default:
					return call_user_func_array(array($instance, $method), $args);
			}
			
			//

		}
		
	}