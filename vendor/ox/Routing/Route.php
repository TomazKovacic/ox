<?php
namespace ox\Routing;
use Closure;

class Route {

  private $method;
  private $path;
  private $destination;
  private $parameters = array();
  
  private $name;
  private $before = array();
  private $after = array();
  private $middleware;
  private $namespace;
  private $prefix;
        
  public function __construct($method, $path, $destination, $parameters) {
      
      $this->method = $method;
      $this->path = $path;
      $this->destination = $destination;
      $this->parameters = $parameters;
      
      
  }

  public function getMethod() {
      return $this->method;
  }

  public function getPath() {
      return $this->path;
  }


  public function getDestination() {
      return $this->destination;
  }
  //--
  
  public function getName() {
      return $this->name;
  }
  
  public function setName($name) {
      $this->name = $name;
  }


  public function getMiddleware() {
      return $this->middleware;
  }
  
  public function setMiddleware(array $middlewareArray) {
    if(!is_array($middlewareArray)) {
        throw new Exception("Middware argument must be an array");
    }
    $this->middleware = $middlewareArray;
  }
  
  public function addMiddleware(array $middleware) {
    $this->middleware[] =  $middleware;
  }
  
  public function getNamespace() {
      return $this->namespace;
  }
  
  public function setNamespace($namespace) {
      $this->namespace = $namespace;
  }
  
  public function getPrefix() {
      return $this->prefix;
  }

  public function getBefore() {
      return $this->before;
  }
    
  public function setBefore($beforeArray) {
    if(!is_array($beforeArray)) {
        throw new \Exception("Before argument must be an array");
    }
    $this->before =  array_unique(array_merge($this->before, $beforeArray));
  }

  public function getAfter() {
      return $this->after;
  }
    
  public function setAfter($afterArray) {
    if(!is_array($afterArray)) {
        throw new \Exception("After argument must be an array");
    }
    $this->after =  array_unique(array_merge($this->after, $afterArray));
  }

    
  public function setPrefix($prefix) {
      $this->prefix = $prefix;
  }
  
  public function getParameters() {
      return $this->parameters;
  }
  
  public function setParameters($parameters) {
      //print 'Route::setParamters: ';
      //print_r2($parameters);
      
      $this->parameters = $parameters;
  }
}
