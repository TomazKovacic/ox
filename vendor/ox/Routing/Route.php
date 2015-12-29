<?php
namespace ox\routing;

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

    global $app;

    $app->router->addRoute($method, $path, $destination, $parameters);

  }
}
