<?php namespace ox\Routing;

class Dispatcher {


  public $result;

  public function __construct() {

	// ..new implementation, maybe cycle app:events, like laravel?

  }

  public function dispatch($request,  $currentRoute) {

    //print '[ASC 7] Dispatcher::dispatch()<br><br>';
    //print_r2_button(':: Current: route');  print_r2($currentRoute);


    // BEFORE

    if( is_array($currentRoute->getBefore()) && (count( $currentRoute->getBefore() )>0) ) {
        foreach( $currentRoute->getBefore() as $before) {
            $filterResult = \Route::runFilter($before, $currentRoute, $request);

            //print 'filter result type: ' . get_class($filterResult) . '<br>';

            //if redirect
            if(  get_class($filterResult) == 'Symfony\Component\HttpFoundation\RedirectResponse' ) {

                $filterResult->send(); exit();
            }
        }
    }



    $destination = $currentRoute->getDestination();

    if(is_string($destination) || is_array($destination) ) {    //string or array

      if(is_string($destination)) {
        list($controllerClass, $controllerAction) = explode('@', $destination);

      } else {  //array

          print 'STOP, FIX ' . __LINE__ . ' ' . __FILE__ . '<br>';

        if( !isset($destination['uses'])) {

          print 'dispatch::dispatch() Error, no destination uses found.<br>';
          return;
        }

        list($controllerClass, $controllerAction) = explode('@', $destination['uses']);

      }


      #print 'test in ' . __LINE__  .',<br>' .  __FILE__ .'<br><br>';


      //prefix with bachslash to escape current namespace
      if( substr( $controllerClass, 0, 1) !== '\\' ) {
          $controllerClass = '\\' . $controllerClass;
      }

      try {

        $controller = new $controllerClass();
        //print_r2($controller);
        //exit();
        #print 'cc: '.$controllerClass; exit();
      }
      catch (Exception $e) {
        print 'Exception: '.  $e->getMessage(). "<br>\n";
      }
      //print_r2($currentRoute);

      try {

        $parameters = ($currentRoute->getParameters())? array_values($currentRoute->getParameters()) : array();
        //$parameters = array_values( $currentRoute->getParameters() );

        #print 'stop in ' . __LINE__  .',<br>' .  __FILE__; exit();

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


      //print '[ASC 7 A] Dispatcher::dispatch() $controllerClass: <b>'. $controllerClass . '</b>, $controllerAction: <b>'. $controllerAction .'</b><br>';
      //print '[ASC 7 B] Dispatcher::dispatch() Name = ' . '\\' . $controllerClass . '<br>';

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
          $result = new \Symfony\Component\HttpFoundation\Response( $result );

      } elseif( (get_class($result)                     == 'Symfony\Component\HttpFoundation\Response') ||
              (get_parent_class( get_class($result))  == 'Symfony\Component\HttpFoundation\Response') ) {

      //
      //print 'Dispatcher::dispatch() # resuilt is type '. get_class($result) . '<br>';
      //print_r2($result);
      //

      } else {
        //error
        print 'dispatch::dispatch() Error<br>';
        print '.... response is not string type ... <br>';
        print 'object type is ' . get_class($result) . ' <br>';

        print '<br>';
        print 'Destination: '. $destination  .'<br>';

        return;

    	//print 'Exception: '.  $e->getMessage(). "<br>\n";
    }
    print '';
    if( is_array($currentRoute->getAfter()) && (count( $currentRoute->getAfter() )>0) ) {
        foreach( $currentRoute->getAfter() as $after) {
             $result = \Route::runFilter($after, $currentRoute, $request, $result);
        }
    }

    return $result;

  }


}
