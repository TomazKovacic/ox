<?php namespace ox\routing;

class Dispatcher {
	
  public $status = -1; //undefined
  public $moduleName;
  public $result;

  public function __construct() {
	
	// ..new implementation, maybe cycle app:events, like laravel?

  }

  public function dispatch($request,  $currentRoute) {

    //print '[ASC 7] Dispatcher::dispatch()<br><br>';

    //print_r2_button('Route current'); print_r2($currentRoute);

    $destination = $currentRoute['destination'];

    try {
      list($controllerClass, $controllerAction) = explode('@', $destination);
    }
    catch (Exception $e) {
      throw new \Exception('Dispatch FAIL');
      print 'Exception: '.  $e->getMessage(). "<br>\n";
    }


    //print '[ASC 7] Dispatcher::dispatch() $controllerClass: <b>'. $controllerClass . '</b>, $controllerAction: <b>'. $controllerAction .'</b><br>';
    //print '[ASC 7] Dispatcher::dispatch() Name = ' . '\\' . $controllerClass . '<br>';

    //prefix with bachslash to escape current namespace
    if( substr( $controllerClass, 0, 1) !== '\\' ) {
    	$controllerClass = '\\' . $controllerClass;
    }

    try {
      $controller = new $controllerClass();
    } 
    catch (Exception $e) {
      print 'Exception: '.  $e->getMessage(). "<br>\n";
    }

    try {
      $result = $controller->$controllerAction(); 
    }
    catch (Exception $e) {
      print 'Exception: '.  $e->getMessage(). "<br>\n";
    }



    if(is_string($result)) {
    	return new \ox\Framework\Response( $result );

    } else {
    	print '.... response is not string type ... <br>'; //error

    	//print 'Exception: '.  $e->getMessage(). "<br>\n";
    }

    
  }	


}