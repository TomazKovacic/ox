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

      #print get_class($this->current); exit();

      if( is_object($this->current) && (get_class($this->current) == 'ox\Routing\Route') ) {

        return $this->dispatcher->dispatch($request,  $this->current);

      } else {

            //return status 404, not found
            return new \Symfony\Component\HttpFoundation\Response('Error: route not found', 404);
      }

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
        //print basename(__FILE__).'/'. __LINE__ . ' #1 groupStack snapshot: '; print_r2($this->groupStack); print '<br>';
        
        call_user_func($callback, $this);
        array_pop($this->groupStack);
        
        //print basename(__FILE__).'/'. __LINE__ . ' #2 groupStack snapshot: '; print_r2($this->groupStack); print '<br>';
    }

    protected function updateGroupAttributes(array $attributes) {
        
        if (! empty($this->groupStack)) {
            $attributes = array_merge($attributes, end($this->groupStack));
        }        
        $this->groupStack[] = $attributes;
    }

    public function getRoutes() {
        return $this->routeCollection;
    }


    public static function addRoute($method, $path, $destination, $parameters = null ) {

    $app = app();

    //$des = is_string($destination)?$destination: '?'; print 'Router::addRoute('. $method .', '. $path .', '. $des . ') ... <br>';
    //print_r2($app);
    
    $route = static::createRoute($method, $path, $destination, $parameters);

    $app['Route']->routeCollection->add($route);
  }
  
   protected static function createRoute($method, $path, $destination, $parameters)
   {
       $route = new Route($method, $path, $destination, $parameters);
       
       //print basename(__FILE__).'/'. __LINE__ . ' @' . __FUNCTION__ . ';<br>';
       //print 'New route:'; print_r2($route);
       
       $app = app();
       
       $groupStack = $app['Route']->groupStack;
       
       if( is_array($groupStack) && (count($groupStack)>0) ) {
           
           $group = $app['Route']->groupStack[count($app['Route']->groupStack) - 1];

            foreach ($group as $name => $val) {
                //print 'GS: name:' .$name. ' val:'. $val .'<br>';

                switch ($name) {
                    case 'middleware';
                        if (is_string($val)) {
                            $val = [$val];
                        }
                        $route->setMiddleware($val);
                        break;
                    case 'prefix':
                        $route->setPrefix($val);
                        break;
                    case 'namespace':
                        $route->setNamespace($val);
                        break;

                    default:
                        print 'unknowwn group setting<br>';
                }
            }

            //print 'Stack:'; print_r2( $app['Route']->groupStack );
       }
       
       //print 'Route after:'; print_r2($route);
       

       
       
       return $route;
   }

}
