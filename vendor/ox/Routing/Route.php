<?php
namespace ox\routing;

class Route {

  private $method;
  private $path;
  private $destination;
  private $options;

  static function get($path, $destination, $options = null) {

//print 'ox\Classes\route::get <br>';

    route::addRoute('GET', $path, $destination, $options);
  }

  static function post($path, $destination, $options = null) {

    route::addRoute('POST', $path, $destination, $options);
  }


  static function addRoute($method, $path, $destination, $options) {

    $debug = false;   //local debug switch
    //$debug = true;

    if( $debug === true) {

      print '<div class="info">' ."\n";
      print 'route::addRoute <br>' ."\n";
      print '- method: '. $method .' <br>' ."\n";
      print '- path: '. $path .' <br>' ."\n";
      print '- destination: '. $destination .' <br>' ."\n";
      print '</div>' ."\n";
    }

    global $app;

    $app->router->addRoute($method, $path, $destination, $options);

  }
}
