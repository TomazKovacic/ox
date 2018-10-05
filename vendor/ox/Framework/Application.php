<?php namespace ox\framework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpKernel\HttpKernelInterface;

  class Application implements \ArrayAccess, HttpKernelInterface {

    public $version = '2.0';
    public $bindings = array();
    public $request;
    public $config = array();

    public $aliases = array();
    protected $middlewares = array();

    // -----------------------------------------------------

    public function __construct() {

      #$this->router    = new \ox\Routing\Router();
      $this->request   = Request::createFromGlobals(); //= new Request();
      $this->response  = new Response();

      $session = new Session();
      $session->start();
      $this->request->setSession($session);

      $this->registerBaseMiddlewares();
    }

    // -----------------------------------------------------

    public function bind($name, $obj) {
      $this->bindings[$name] = $obj;
    }

    // -----------------------------------------------------

    //public function bindShared($name, $closure) {
    //
    //  $this->bind($name, $closure);
    //}

    // -----------------------------------------------------

    public function bindClass($name, $className) {
      // old$this->bindings[$name] =  new $className;

      //klic service providerja
      //$this->bindings[$name] =  $this->register($className);
      // ?? $this->register($className);
    }

    // -----------------------------------------------------
    public function register($provider) {

      //print 'registering ... provider <br>';

      $r = $provider->register();

      //dd($this);
    }

    // -----------------------------------------------------
    // ArrayAccess, mandarory

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->bindings);
    }

    public function offsetGet($offset) { //print_r2(short_trace(debug_backtrace(),1 ));
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

        //'Auth'      => 'ox\Auth\AuthManager',
        //'DB'        => 'ox\Database\DatabaseManager',
        //'encrypter' => 'ox\Encryption\Encrypter',
        //'Form'    => 'ox\Html\FormBuilder',
        //'HTML'    => 'ox\Html\HtmlBuilder',
        //'Input'   => 'ox\Classes\Input',
        //'Lang'    => 'ox\Classes\Lang',
        //'Request' => 'ox\Classes\Request',
        //'Redirect' => 'ox\Routing\Redirector',
        //'Route'    => 'ox\Routing\Router',
        //'Session' => 'ox\Classes\Session',
        //'URL'     => 'ox\Classes\URL',
        //'View'     => 'ox\View\View'
      );

      foreach ($aliases as $key => $alias) {
        $this->alias($key, $alias);
      }

    }
    // -----------------------------------------------------

    public function alias($abstract, $alias)
  	{
  		$this->aliases[$alias] = $abstract;
  	}
    // -----------------------------------------------------

    public function loadProviders($providers) {

      foreach($providers as $provider) {

        //print 'loading provider ' . $provider . '<br>';
        $this->register($this->createProvider($provider));
      }

    }
    // -----------------------------------------------------

    public function createProvider($provider) {
      return new $provider($this);
    }

    // -----------------------------------------------------

    private function dispatch(Request $request) {


      return $this['router']->dispatch($request); //returrns Response object
    }

    // -----------------------------------------------------
    //

    protected function getStackedClient() {

      $client = new \Stack\Builder();

      //print 'app: getStackedClient TEST: middlewares: <br>'; print_r2($this->middlewares);


      $client->push('ox\Cookie\Guard', $this['encrypter']);
      $client->push('ox\Cookie\Queue', $this['cookie']);
      //
      $client->push('ox\Session\Middleware', $this['session']); //tmp, gre samo skozi


      $this->mergeCustomMiddlewares($client);


      return $client;
    }



    // -----------------------------------------------------

  protected function mergeCustomMiddlewares(\Stack\Builder $stack)
  {
    foreach ($this->middlewares as $middleware)
    {
      list($class, $parameters) = array_values($middleware);

      array_unshift($parameters, $class);

      call_user_func_array(array($stack, 'push'), $parameters);
    }
  }
    // -----------------------------------------------------

  protected function registerBaseMiddlewares()
  {
    $this->middleware('ox\Middleware\FrameGuard');
  }
    // -----------------------------------------------------

  public function middleware($class, array $parameters = array()) {
    $this->middlewares[] = compact('class', 'parameters');

    return $this;
  }
    // -----------------------------------------------------

    /**
    * handle je previdan za vklučutev middlaware dodatkov, nato v dispatch
    */

	  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {

      return $response = $this->dispatch($request);
  	}

    // -----------------------------------------------------

    public function run(Request $request = null) {

      if (null === $request) {
              $request = Request::createFromGlobals();
      }

  	  $stack = $this->getStackedClient();
  	  $kernel = $stack->resolve($this);

  	  $response = $kernel->handle($request);
  	  $response->send();

      if ($kernel instanceof TerminableInterface) {
          $kernel->terminate($request, $response);
      }
    }
    // -----------------------------------------------------

  } //class end.
