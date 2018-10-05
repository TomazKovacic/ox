<?php
namespace ox\Routing;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class Redirector {

	private $app;

	public $cookies = array();
	public $cookies_to_remove = array();

	function __construct(){
		//print 'Redirector::construct <br>';
		$this->app = app();
	}


	public function setCookie($name, $value) {
		$this->cookies[$name] = $value;
	}

	public function removeCookie($name) {
		$this->cookies_to_remove[$name] = $name;
	}

	public function home($status = 302,  $headers = array()) {
		//print 'Redirector::home() <br>';

		$path = '/';
		return $this->to($path, $status, $headers);

	}


	public function back($status = 302, $headers = array()) {
		//print 'Redirector::back() <br>';

		$request = $this->app->request;


		return $this->to($request->headers->get('referer'));

	}

	public function refresh($status = 302, $headers = array()) {
		print 'Redirector::refresh() <br>';
	}

	public function intended($default = '/', $status = 302, $headers = array(), $secure = null) {

		print 'Redirector::intended() <br>';
	}



	//public function to($path, $status = 302, $headers = array(), $cookies = array()) {
	public function to($path, $status = 302, $headers = array()) {

		//print 'Redirector::to() <br>';
		//print('cookies: '); print_r2($this->cookies);


		//print ':::: ' . $path  .', '. $status  . ',  ['.  implode(', ', $headers) . '] <br>';
		//exit();

		$rsp = new RedirectResponse($path, $status, $headers);

		if(!empty($this->cookies)) {
			foreach($this->cookies as $ckey=>$cval) {
				if( (is_object($cval)) && (get_class($cval) == 'Symfony\Component\HttpFoundation\Cookie') ) {
					$rsp->headers->setCookie($cval);
				} else {
					$rsp->headers->setCookie(  new Cookie($ckey, $cval,  time()+(3600*6), '/', null, false, false) );
				}

			}
		}

		if(!empty($this->cookies_to_remove)) {
			foreach($this->cookies_to_remove as $name) {
				$rsp->headers->clearCookie( $name );
			}
		}

		// T-77

		//$rsp->send();
		//print_r2( $rsp->headers ) ;
		//exit();

		return $rsp;
	}

	public function away($path, $status = 302, $headers = array()) {
			print 'Redirector::away() <br>';
	}

	public function secure($path, $status = 302, $headers = array()) {
		print 'Redirector::secure() <br>';
	}

	public function route($route, $parameters = array(), $status = 302, $headers = array()) {
			print 'Redirector::route() <br>';
	}

}
