<?php

namespace ox\Middleware;

use Symfony\Component\HttpKernel\HttpKernelInterface; 
use Symfony\Component\HttpFoundation\Request;

class FrameGuard implements HttpKernelInterface
{

	protected $app;


	public function __construct(HttpKernelInterface $app)
	{
		$this->app = $app;
	}	

    /**
     * Handle the given request and get the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		$response = $this->app->handle($request, $type, $catch);

		$response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);

		return $response;
	}
}