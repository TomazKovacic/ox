<?php namespace ox\routing;


class RouteCollection {


  public $routes = array();

  private $parameters = array();   //for temp storage of route parameters
                                   //ie. /page/{id}, /page/123  => ['id' => 123]

  public function __construct() {}

  public function add($method, $path, $destination, $parameters) {

    $this->routes[] = array(
        'method'      => $method,
        'path'        => $path,
        'destination' => $destination,
        'parameters'     => $parameters );

    //print 'RouteCollection::add() route added. path: '. $path .'<br><br>';
  }


  public function match( \Symfony\Component\HttpFoundation\Request $request ) {

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

  public function check( \Symfony\Component\HttpFoundation\Request $request, array $routes ) {


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
          $path = $rt['path'];

          //print '5: Compare rt[path]: ' . $rt['path'] . ' vs $pathinfo: ' . $pathinfo . '<br>';
          if($path ==  $pathinfo) {
            //print '** Route found: ' . $pathinfo . '<br>';
            return $rt;
          }

          //regex, does it contain '{' '}'
          if( ( strstr($path, '{') !== false) && ( strstr($path, '{') !== false) ) {

            //print 'found). '. $path .' <br>';

            if($this->regexCheck($rt['path'], $pathinfo) == true) {
            
              $rt['parameters'] = $this->parameters;

              //print '$$ '; print_r2($rt);
              return $rt;
            }
          }
      }
    } else {

      print 'RouteCollection::check() Route not found(1): ' . $pathinfo . '<br>';
      //return reset($this->routes);

      return false;
    }



    return false;
  }

  private function regexCheck($routePath, $pathinfo) {

    //print 'regexCheck: $routePath: <u>'. $routePath .'</u>, to $pathinfo <u>' . $pathinfo . '</u> <br><br>';

    $routeParts = explode('/', $routePath);
    $pathinfoParts = explode('/', $pathinfo);

    //print_r2($routeParts);
    //print_r2($pathinfoParts);

    if( count($routeParts) !== count($pathinfoParts)) { 
      //print '5 A # Parts count mismatch, false. <br><br>';
      return false;
    }

    foreach($routeParts as $ix=>$part) {

      //print '## ' . $routeParts[$ix] . ' vs ' . $pathinfoParts[$ix] . ' <br>'; 

      if( (substr($routeParts[$ix],0, 1)  === '{') &&   (substr($routeParts[$ix], -1) === '}') ) {
          $key = substr($routeParts[$ix],1, -1);
           //print '## is REGEX: ' . $pathinfoParts[$ix] . ', key = '. $key .'<br>'; 
           $this->parameters[$key] = $pathinfoParts[$ix];
      
      } else {
          if ( $routeParts[$ix] !== $pathinfoParts[$ix] ) {
            //print '5 A # Parts compare mismatch, false. <br><br>';
            return false;
          }
      }
    }

    return true;

    //preg_match("/\{(.*?)\}/", $input_line, $output_array);
    /*
    a. razbuj po slashih  ie. explode '/' v -> parts
    b. primerjaj število parts
    c. ali so ne-regex parts isti
    d. če da, ok -> return true

    algoritem uporabi za izvleko parametrog v regex some/{name} vs some/123  --> ['name' => 123]

    */
  }

}
