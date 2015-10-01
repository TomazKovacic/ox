<?php namespace ox\framework;


  //use ox\framework\RouteCollection;


  class Application implements \ArrayAccess {


    public $version = '0.1b';
    public $bindings = array();

    public $request;

    public $config = array();

    public $logs = array();
    public $errors = array();

    public $content = array();
    public $data = array();

    public $output = '';

    public $renderer;

    public $aliases = array();


  
    // -----------------------------------------------------

    public function __construct() {

      global $config;

      $this->router    = new \ox\routing\Router();
      $this->request   = new Request();
      $this->response  = new Response();

      $this->config = $config;

      

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
    // ArrayAccess mandarory

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


      //print_r2($request);
      $d = $this->router->dispatch($request); //returrns Response object

      //return new Response('aaaaaaa');
      return $d;
    }

    // -----------------------------------------------------

    public function render() {

		//@todo rewrite

      //$this->output = $this->renderer->render( $this->content, $this->data );

    }


    // -----------------------------------------------------

    public function output() {

        print $this->output;
    }

    // -----------------------------------------------------

    public function addLog( $logArray ) {
      $this->logs[] = $logArray;
    }

    // -----------------------------------------------------

    public function getLogs() {
      return $this->logs;
    }
    // -----------------------------------------------------

    public function addError( $errorArray ) {
      $this->errors[] = $errorArray;
    }

    // -----------------------------------------------------

    public function getErrors() {
      return $this->errors;
    }
    // -----------------------------------------------------

    /**
    * handle je previdan za vklučutev middlaware dodatkov, nato v dispatch
    */
	
	  public function handle(Request $request) {
				

      //@todo middleware mumbo TK


      return $response = $this->dispatch($request);


  	}
  	
  	// -----------------------------------------------------
  	
  	public function run(Request $request = null) {

      //print '[ASC 1] Application::run()<br>';

  		if (null === $request) {
              $request = Request::createFromGlobals();
  			
  			//print '--debug test request :<br>';  print('<pre>');print_r($request);print('</pre>');
      }
  		
  		// see: https://github.com/alexbilbie/Proton/blob/master/src/Application.php

      // see: http://symfony.com/doc/current/create_framework/index.html




       		
  		$response = $this->handle($request);

      //debug_table( debug_backtrace(), __FILE__, __LINE__ );

      //if(GDEBUG === true) print '<br><br>[DEC 1] Application::run() Response:'; print '<pre>'; print_r($response); print '</pre>';

      if( 200 ==  $response->getStatusCode() ) {

  		  $response->send();

      } else {
          $response->sendHeaders();
          exit();
          //print 'Status: ' . $response->getStatusCode() . '<br>';  
      }


      
      

      //
      print '<br><br>'; print_r2_adv($this); 
  		  		
  	}


  } //class end.
