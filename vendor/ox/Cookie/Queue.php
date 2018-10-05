<?php namespace ox\Cookie;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Queue implements HttpKernelInterface {

  protected $app;

  protected $encrypter;

  public function __construct(HttpKernelInterface $app, CookieJar $cookies) {
		$this->app = $app;
		$this->cookies = $cookies;
	}

  public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
		$response = $this->app->handle($request, $type, $catch);
		foreach ($this->cookies->getQueuedCookies() as $cookie) {
			$response->headers->setCookie($cookie);
		}
		return $response;
	}


}
