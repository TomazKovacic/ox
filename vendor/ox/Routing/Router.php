<?php namespace ox\Routing;

class Router {

    protected $routeCollectionection;
    protected $events; //na
    protected $current;
    protected $dispatcher;
    protected $filters = array();

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

      $this->current = $this->routeCollection->match($request);

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

    //new

    static function multiadd($array, $destination, $parameters = null) {

      if( !is_array($array)) {
        print 'Route::multiadd: first parameter must be array';
        return '';
      }

      foreach($array as $method => $routes) {


        $allowed_methods = array('get', 'post', 'put', 'patch', 'delete', 'options');

        if( in_array(strtolower($method), $allowed_methods)) {

          foreach($routes as $rt) {

            self::addRoute(strtoupper($method), $rt, $destination, $parameters);
          }
        }
      }

    }

    static function multiaddFn($fn, $destination, $parameters = NULL ) {

      if(function_exists($fn)) {
          $paths = $fn();

      } else {

        preg_match('!(\w+).*?(\w+)!', $fn, $m);
        $fnClass = $m[1];
        $fnMethod = $m[2];


        $paths = call_user_func_array( [$fnClass, $fnMethod ], (array)$parameters );
      }

      if( !is_array($paths)) {
        print 'Route::multiadd: first parameter must be array';
        return '';
      }

      #foreach($array as $method => $routes) {
        # code...
      #}

      self::multiadd($paths, $destination, $parameters);

    }


    static function match($matchArray, $path, $destination, $parameters = null) { //print 'matchArray:'; print_r2($matchArray);
        if (is_array($matchArray)) {
            foreach ($matchArray as $method) {
                $method = strtolower($method);
                if (in_array($method, array('get', 'post', 'put', 'patch', 'delete', 'options'))) {

                    self::addRoute($method, $path, $destination, $parameters);
                } else {
                    print 'Error: Route::match() method not allowed. Method: ' . $method . '<br>';
                }
            }
        } else {
            print 'Error: Route::match() first parameter is not an array<br>';
        }
    }

    //filters

    public function filter($name, $callback) {
        $this->filters[$name] = $callback;
    }

    static function runFilter($name, $route = NULL, $request = NULL, $response = NULL) { ///$after, $currentRoute, $request, $result

        $app = app();
        //print 'runFilter params: name: ' . $name . ' ... <br>';
        //print_r2( $app['router'] );

        $callback = $app['router']->filters[$name];
        if(is_string($callback)) {

            print 'string implementation missing ... <br>';
            return;
        }
        if($request == NULL) { $request = $app->request; }

        $r = $callback($route, $request, $response);

        return $r;
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

      $app['router']->routeCollection->add($route);
    }

    protected static function createRoute($method, $path, $destination, $parameters) {

       $route = new Route($method, $path, $destination, $parameters);

       //print basename(__FILE__).'/'. __LINE__ . ' @' . __FUNCTION__ . ';<br>';
       //print 'Router:: createRoute: New route: method: ' . $route->getMethod() . ' path: ' . $route->getPath() . '<br>';

       $app = app();

       $groupStack = $app['router']->groupStack;

       if( is_array($groupStack) && (count($groupStack)>0) ) {

           $group = $app['router']->groupStack[count($app['router']->groupStack) - 1];

            foreach ($group as $name => $val) {
                //print 'GS: name:' .$name. ' val:'. $val .'<br>';

                switch ($name) {
                    case 'middleware':
                        if (is_string($val)) {
                            $val = [$val];
                        }
                        $route->setMiddleware($val);
                        break;
                    case 'before':
                        if (is_string($val)) { $val = array($val); }
                        $route->setBefore($val);
                        break;
                    case 'after':
                        if (is_string($val)) { $val = array($val); }
                        $route->setAfter($val);
                        break;
                    case 'prefix':
                        $route->setPrefix($val);
                        break;
                    case 'namespace':
                        $route->setNamespace($val);
                        break;

                    default:
                        print 'WARNING: unknowwn group setting: '. $name . '<br>';
                }
            }

            //print 'Stack:'; print_r2( $app['router']->groupStack );
       }


       return $route;
   }

   public static function routeTable() {

     $app = app();
     //$c = get_r2( $app['router']->routeCollection );
     //print_r2($app['router']->routeCollection->routes);

     if( is_array($app['router']->routeCollection->routes) ) {

       //$c = 'r is array';
       $c = '<table>';
       $c .= '<tr>';
       $c .= '<th>Method</th><th>Path</th><th>Destination</th><th>Parameters</th><th>Name</th>';
       $c .= '<th>Bofore</th><th>After</th><th>Middleware</th><th>Namespace</th><th>Prefix</th>';
       $c .= '</tr>';
       foreach( $app['router']->routeCollection->routes as $rt) {

#if($xx<1) {print_r2($rt); $xx=1; }
#print_r2($rt);

         $c .= '<tr>';
         $c .= '<td>'.$rt->getMethod().'</td>';
         $c .= '<td>'.$rt->getPath().'</td>';
         $c .= '<td>'.$rt->getDestination().'</td>';
         $p= $rt->getParameters();
         if( is_array($p)) { $pp = implode(', ', $p); } else { $pp = ' / ' ;}
         $c .= '<td>'. $pp .'</td>';
         $c .= '<td>'.$rt->getName().'</td>';

         $b = $rt->getBefore();
         if( is_array($b)) { $bb = implode(', ', $b); } else { $bb = ' / ' ;}
         $c .= '<td>'.$bb.'</td>';

         $a = $rt->getAfter();
         if( is_array($a)) { $aa = implode(', ', $a); } else { $aa = ' / ' ;}
         $c .= '<td>'.$aa. '</td>';

         $m = $rt->getMiddleware();
         if( is_array($m)) { $mm = implode(', ', $m); } else { $mm = ' / ' ;}
         $c .= '<td>'.$mm. '</td>';

         $n = $rt->getNamespace();
         if( is_array($n)) { $nn = implode(', ', $n); } else { $nn = ' / ' ;}
         $c .= '<td>'.$nn. '</td>';

         $c .= '<td>'.$rt->getPrefix().'</td>';

         $c .= '</tr>';


       }
       $c .= '</table>';
     } else {
       $c = 'No routes.';
     }


     return $c;

   }

}
