<?php namespace ox\routing;

class Dispatcher {


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
      //print 'Test Parameters:<br>';
      //print_r2 ( $currentRoute['parameters'] );

      $parameters = ($currentRoute['parameters'])? array_values($currentRoute['parameters']) : array();


      //dumb code, but faster
      switch (count($parameters)) {
        case 0:
          $result = $controller->$controllerAction();
          break;
        case 1:
          $result = $controller->$controllerAction($parameters[0]);
          break;
        case 2:
          $result = $controller->$controllerAction($parameters[0], $parameters[1]);
          break;
        case 3:
          $result = $controller->$controllerAction($parameters[0], $parameters[1], $parameters[2]);
          break;
        default:
          $result = call_user_func_array(array($controller, $controllerAction),$parameters);
        }
    }
    catch (Exception $e) {
      print 'Exception: '.  $e->getMessage(). "<br>\n";
    }



    if(is_string($result)) {
    	return new \Symfony\Component\HttpFoundation\Response( $result );

    } else {
    	print '.... response is not string type ... <br>'; //error

    	//print 'Exception: '.  $e->getMessage(). "<br>\n";
    }


  }


}
