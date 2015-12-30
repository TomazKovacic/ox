<?php
namespace ox\Routing;

class Route {

  private $method;
  private $path;
  private $destination;
  private $parameters;

  static function get($path, $destination, $parameters = null) {
//print 'ox\Classes\route::get <br>';
    route::addRoute('GET', $path, $destination, $parameters);
  }

  static function post($path, $destination, $parameters = null) {
    route::addRoute('POST', $path, $destination, $parameters);
  }

  static function put($path, $destination, $parameters = null) {
    route::addRoute('PUT', $path, $destination, $parameters);
  }

  static function patch($path, $destination, $parameters = null) {
    route::addRoute('PATCH', $path, $destination, $parameters);
  }

  static function delete($path, $destination, $parameters = null) {
    route::addRoute('DELETE', $path, $destination, $parameters);
  }

  static function options($path, $destination, $parameters = null) {
    route::addRoute('OPTIONS', $path, $destination, $parameters);
  }

  static function any($path, $destination, $parameters = null) {
    route::addRoute('GET|POST|PUT|PATCH|DELETE', $path, $destination, $parameters);
  }

  static function match($matchArray, $path, $destination, $parameters = null) {
    if( is_array($matchArray)) {
      foreach($matchArray as $method) {
        if(in_array($method, array('get', 'post', 'put', 'patch', 'delete', 'options') )) {
          $method = strtolower($method);
          route::addRoute('OPTIONS', $path, $destination, $parameters);
        } else { print 'Error: Route::match() method not allowed. Method: ' . $method . '<br>';}
      }
    } else {
      print 'Error: Route::match() first parameter is not an array<br>';
    }
  }

  static function group(array $attributes, Closure $callback) {
    $this->updateGroupAttributes($attributes);
    call_user_func($callback);
    array_pop($this->groupStack);
  }


  static function addRoute($method, $path, $destination, $parameters) {

    $debug = false;   //local debug switch
    //$debug = true;

    if( $debug === true) {

      print '<div class="info">' ."\n";
      print 'route::addRoute <br>' ."\n";
      print '- method: '. $method .' <br>' ."\n";
      print '- path: '. $path .' <br>' ."\n";
      print '- destination: ';
      if(is_string($destination)) {
        print $destination;
      } elseif(is_array($destination)) {
        print '[' . $destination . ']';
      } elseif(is_object($destination)) {
        print get_class($destination);
      } else {
          print '- ? -';
      }
      print '<br>' ."\n";

      print '</div>' ."\n";
    }

    //global $app;
    if(strstr($method, '|')) {
      //print 'method pipes found.';
      $methods = explode('|', $method);
      foreach($methods as $mt) {
        app()->router->addRoute($mt, $path, $destination, $parameters);
      }
    } else {
      app()->router->addRoute($method, $path, $destination, $parameters);
    }

  }
}
