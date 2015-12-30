<?php namespace ox\framework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

  class Application implements \ArrayAccess {

    public $version = '0.04';
    public $bindings = array();
    public $request;
    public $config = array();

    public $aliases = array();

    // -----------------------------------------------------

    public function __construct() {

      $this->router    = new \ox\Routing\Router();
      $this->request   = Request::createFromGlobals(); //= new Request();
      $this->response  = new Response();

      $session = new Session();
      $session->start();
       $this->request->setSession($session);
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
        'Redirect' => '\ox\Routing\Redirector',
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
    //

    public function getStackedClient() {

      $client = new \ox\stack\builder();

      //$client->push('Illuminate\Cookie\Guard', $this['encrypter']);
      //$client->push('Illuminate\Cookie\Queue', $this['cookie']);
      //$client->push('Illuminate\Session\Middleware', $this['session']);



      return $client;
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

      //original, plain
      $response = $this->handle($request);

      //stack

      //STACK $stack = $this->getStackedClient();
      //STACK $response = $stack->handle($request);

      //print 'App:run(), back with $response <br>';
      //print_r2_adv($response);
      //print htmlentities($response); //exit();

      if( get_class($response) == 'Symfony\Component\HttpFoundation\Response' ) {
        $response->send();

      } elseif( get_class($response) == 'Symfony\Component\HttpFoundation\RedirectResponse' ) {
        //print $response;
        //print htmlentities($response);
        //$response = new \Symfony\Component\HttpFoundation\Response($response );
        $response->send();

      } elseif( is_string($response) ) {
          print $response;
      }



      //print '<br><br>:'; print_r2_adv($this);


    }
    // -----------------------------------------------------

  } //class end.
