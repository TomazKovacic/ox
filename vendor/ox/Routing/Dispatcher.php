<?php namespace ox\Routing;

class Dispatcher {


  public $result;

  public function __construct() {

	// ..new implementation, maybe cycle app:events, like laravel?

  }

  public function dispatch($request,  $currentRoute) {

    //print '[ASC 7] Dispatcher::dispatch()<br><br>';
    //print_r2_button(':: Current: route'); print_r2($currentRoute);

    $destination = $currentRoute['destination'];

    if(is_string($destination) || is_array($destination) ) {    //string or array

      if(is_string($destination)) {
        list($controllerClass, $controllerAction) = explode('@', $destination);

      } else {  //array

        if( !isset($destination['uses'])) {

          print 'dispatch::dispatch() Error, no destination uses found.<br>';
          break;
        }

        list($controllerClass, $controllerAction) = explode('@', $destination['uses']);

      }


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

      //print '[ASC 7] Dispatcher::dispatch() $controllerClass: <b>'. $controllerClass . '</b>, $controllerAction: <b>'. $controllerAction .'</b><br>';
      //print '[ASC 7] Dispatcher::dispatch() Name = ' . '\\' . $controllerClass . '<br>';

    // --------------
    // --------------

    } else {
      //print '*** else, type is ' . gettype($destination). ' class is ' . get_class($destination) . '<br>';

      $result = $destination();

      //print '*** result type is : ' . gettype($result);
      //if(gettype($result) == 'object') { print 'result class is : ' . get_class($result); }

      //print_r2($result);
      //return $result;
    }



    if(is_string($result)) {
    	return new \Symfony\Component\HttpFoundation\Response( $result );

    } elseif( (get_class($result)                     == 'Symfony\Component\HttpFoundation\Response') ||
              (get_parent_class( get_class($result))  == 'Symfony\Component\HttpFoundation\Response') ) {

      //print 'Dispatcher::dispatch() # resuilt is type '. get_class($result) . '<br>';
      //print_r2($result);

      return $result;


    } else {
       //error
      print 'dispatch::dispatch() Error<br>';
    	print '.... response is not string type ... <br>';
      print 'object type is ' . get_class($result) . ' <br>';



    	//print 'Exception: '.  $e->getMessage(). "<br>\n";
    }


  }


}
