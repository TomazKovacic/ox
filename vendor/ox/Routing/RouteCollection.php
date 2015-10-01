<?php namespace ox\routing;


class RouteCollection {


  public $routes = array();

  public function __construct() {}

  public function add($method, $path, $destination, $options) {

    $this->routes[] = array(
        'method'      => $method,
        'path'        => $path,
        'destination' => $destination,
        'options'     => $options );

    //print 'RouteCollection::add() route added. path: '. $path .'<br><br>';
  }


  public function match( \ox\framework\Request $request ) {

    //print '[ASC3] RouteCollection::match() <br><br>';

    //print '[ASC3] &gt; RouteCollection::match() Test: Method is: ' . $request->getMethod() . '<br>'; 
    //print '[ASC3] &gt; RouteCollection::match() Test: All routes in collection: <br>';

    //print_r2_button('Routes'); print_r2( $this->routes);

    //print 'RouteCollection::match() Test: Request object: <br>';
    //print_r2_button('Rq'); print_r2( $request );
    //print 'Method: ' . $request->getMethod() . '<br>';

    $routes = $this->get( $request->getMethod() );  //array, vrni vse route dane metode

    $route = $this->check( $request, $routes );


    return $route;

  }

  public function get($method) {

    //print '[ASC 4]  RouteCollection::get() method = '. $method .'<br>';
    $picked_routes = array();

    if( is_array($this->routes) && (count($this->routes)>0) ) {
      foreach($this->routes as $rt) {
          if($rt['method'] == $method) {
            $picked_routes[] = $rt;
          }
      } 
    }

    return $picked_routes;
  }

  public function check( \ox\framework\Request $request, array $routes ) {


    //print '[ASC 5]  RouteCollection::check() <br>';

    //print_r2_button('check(): request'); print_r2(  $request );

    //print '5: Test:  $request->getPathInfo(): ' . $request->getPathInfo() . '<br>';
    //print '5: Test:  $request->getRequestUri(): ' . $request->getRequestUri() . '<br>';
    //print '5: Test:  $request->getBaseUrl(): ' . $request->getBaseUrl() . '<br>';
    //print '5: Test:  $request->getBasePath(): ' . $request->getBasePath() . '<br>';

    $pathinfo = $request->getPathInfo();

    if((substr( $pathinfo, 0, 1)=='/') && ($pathinfo != '/')) {
       $pathinfo =  substr($pathinfo, 1);
    }


     if( is_array($routes) && (count($routes)>0) ) {
      foreach($routes as $rt) {
          //print '5: Compare rt[path]: ' . $rt['path'] . ' vs $pathinfo: ' . $pathinfo . '<br>';
          if($rt['path'] ==  $pathinfo) {
            //print '** Route found: ' . $pathinfo . '<br>';
            return $rt;
          }
      } 
    } else {
      
      //print 'RouteCollection::check() Route not found(1): ' . $pathinfo . '<br>';
      //return reset($this->routes);

      return false;
    }     



    return false;    
  }

}

