<?php namespace ox\routing;

class Router {

	protected $routes;
	protected $events; //na
	protected $current;
  protected $dispatcher;

    // -----------------------------------------------------

    public function __construct() {

    	//print 'Router :: __construct() ... <br><br>';
    	$this->routes = new RouteCollection;
      $this->dispatcher = new Dispatcher;
    }


    public function dispatch( \Symfony\Component\HttpFoundation\Request $request ) {

	  //print '[ASC 2] Router::dispatch()<br>';

      //@todo callFilter('before')

      $this->current = $this->routes->match($request);

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




  public function addRoute($method, $path, $destination, $parameters = null ) {

    //print 'Router::addRoute('. $method .', '. $path .', '. $destination . ') ... <br>';

    $this->routes->add($method, $path, $destination, $parameters);
  }

}
