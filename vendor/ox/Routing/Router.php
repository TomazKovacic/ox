<?php namespace ox\Routing;

class Router {

    protected $routeCollectionection;
    protected $events; //na
    protected $current;
    protected $dispatcher;
    
    protected $groupStack = array();

    // -----------------------------------------------------

    public function __construct() {

        //print 'Router :: __construct() ... <br><br>';
        //print '<pre>'; debug_print_backtrace();
        
        $this->routeCollection = new RouteCollection;
        $this->dispatcher = new Dispatcher;
        //$groupStack = array('time' => date('H:i:s') );
    }

    public function dispatch( \Symfony\Component\HttpFoundation\Request $request ) {

	  //print '[ASC 2] Router::dispatch()<br>';

      //@todo callFilter('before')

      // !!!!
      $this->current = $this->routeCollection->match($request);

      //@todo callFilter('after'); //maybe after this->dispatcher->dispatch?

      //return $this->dispatcher->dispatch($request,  $this->current);

      if( is_array($this->current) && ( $this->current['destination'] ) ) {

        return $this->dispatcher->dispatch($request,  $this->current);

      } else {

          if (false === $this->current ) {

            //return status 404, not found
            return new \Symfony\Component\HttpFoundation\Response('Error: route not found', 404);
          }

      }


      //print_r2($this->current);
      //print '----';
      //print_r2($rs);

      return $rs;

    }


    static function get($path, $destination, $parameters = null) {
        self::addRoute('GET', $path, $destination, $parameters);
    }

    static function post($path, $destination, $parameters = null) {
        self::addRoute('POST', $path, $destination, $parameters);
    }

    static function put($path, $destination, $parameters = null) {
        self::addRoute('PUT', $path, $destination, $parameters);
    }

    static function patch($path, $destination, $parameters = null) {
        self::addRoute('PATCH', $path, $destination, $parameters);
    }

    static function delete($path, $destination, $parameters = null) {
        self::addRoute('DELETE', $path, $destination, $parameters);
    }

    static function options($path, $destination, $parameters = null) {
        self::addRoute('OPTIONS', $path, $destination, $parameters);
    }

    static function any($path, $destination, $parameters = null) {
        self::addRoute('GET|POST|PUT|PATCH|DELETE', $path, $destination, $parameters);
    }

    static function match($matchArray, $path, $destination, $parameters = null) {
        if (is_array($matchArray)) {
            foreach ($matchArray as $method) {
                if (in_array($method, array('get', 'post', 'put', 'patch', 'delete', 'options'))) {
                    $method = strtolower($method);
                    self::addRoute($method, $path, $destination, $parameters);
                } else {
                    print 'Error: Route::match() method not allowed. Method: ' . $method . '<br>';
                }
            }
        } else {
            print 'Error: Route::match() first parameter is not an array<br>';
        }
    }

    //function group(array $attributes, Closure $callback) {
    function group(array $attributes, $callback) {
        
        
        $this->updateGroupAttributes($attributes);
        $app1 = app();
        call_user_func($callback, $this);
        array_pop($this->groupStack);
        
        $x = $this->groupStack;
    }

    protected function updateGroupAttributes(array $attributes) {
        
        if (! empty($this->groupStack)) {
            $attributes = array_merge($attributes, end($this->groupStack));
        }
        
        //
        $this->groupStack[] = $attributes;
        $app1 = app();
    }

    public function getRoutes() {
        return $this->routeCollection;
    }


    public static function addRoute($method, $path, $destination, $parameters = null ) {

    $app = app();

    //$des = is_string($destination)?$destination: '?'; print 'Router::addRoute('. $method .', '. $path .', '. $des . ') ... <br>';
    //print_r2($app);

    $app['Route']->routeCollection->add($method, $path, $destination, $parameters);
  }

}
