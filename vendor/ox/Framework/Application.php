<?php namespace ox\framework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

  class Application implements \ArrayAccess {

    public $version = '0.1b';
    public $bindings = array();
    public $request;
    public $config = array();

    public $aliases = array();

    // -----------------------------------------------------

    public function __construct() {

      $this->router    = new \ox\routing\Router();
      $this->request   = new Request();
      $this->response  = new Response();
    }

    // -----------------------------------------------------

    public function bind($name, $obj) {
      $this->bindings[$name] = $obj;
    }

    // -----------------------------------------------------

    public function bindClass($name, $className) {
      $this->bindings[$name] =  new $className;
    }

    // -----------------------------------------------------
    // ArrayAccess, mandarory

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->bindings);
    }

    public function offsetGet($offset) {
        return $this->bindings[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->bindings[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->bindings[$offset]);
    }

    // -----------------------------------------------------

    public function getConfiguration() {
      return $this->config;
    }

    public function setConfiguration($config) {
      $this->config = $config;
    }

    // -----------------------------------------------------

    public function registerCoreContainerAliases() {

      $aliases = array(

        'Auth'    => 'ox\Auth\AuthManager',
        'DB'      => 'ox\Database\DatabaseManager',
        //'Form'    => 'ox\Html\FormBuilder',
        //'HTML'    => 'ox\Html\HtmlBuilder',
        //'Input'   => 'ox\Classes\Input',
        //'Lang'    => 'ox\Classes\Lang',
        //'Request' => 'ox\Classes\Request',
        'Route'   => '\ox\Routing\Route',
        //'Session' => 'ox\Classes\Session',
        //'URL'     => 'ox\Classes\URL',
        'View'    => 'ox\View\View'
      );

      foreach ($aliases as $key => $alias) {

        //print '[C] binding class ' . $alias . ' to key ' . $key . '<br>';
        $this->bindClass($key, $alias);
      }

    }

    // -----------------------------------------------------

    private function dispatch(Request $request) {

      return $this->router->dispatch($request); //returrns Response object
    }

    // -----------------------------------------------------

    /**
    * handle je previdan za vklučutev middlaware dodatkov, nato v dispatch
    */

	  public function handle(Request $request) {

      return $response = $this->dispatch($request);
  	}

    // -----------------------------------------------------

    public function run(Request $request = null) {

      if (null === $request) {
              $request = Request::createFromGlobals();
      }

      $response = $this->handle($request);

      if( 200 ==  $response->getStatusCode() ) {
        $response->send();

      } else {
          $response->sendHeaders();
          //exit();
          print 'Status: ' . $response->getStatusCode() . '<br>';
      }

      //print '<br><br>:'; print_r2_adv($this);
    }
    // -----------------------------------------------------

  } //class end.
