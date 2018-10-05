<?php namespace ox\Cookie;

use ox\Encryption\Encrypter;
use ox\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Guard implements HttpKernelInterface {

  protected $app;

  protected $encrypter;

  public function __construct(HttpKernelInterface $app, Encrypter $encrypter) {
		$this->app = $app;
		$this->encrypter = $encrypter;
	}

  public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
		return $this->encrypt($this->app->handle($this->decrypt($request), $type, $catch));
	}

  protected function decrypt(Request $request) {
		foreach ($request->cookies as $key => $c) {
			try {
				$request->cookies->set($key, $this->decryptCookie($c));
			} catch (DecryptException $e) {
				$request->cookies->set($key, null);
			}
		}

		return $request;
	}

  protected function decryptCookie($cookie) {
		return is_array($cookie)
						? $this->decryptArray($cookie)
						: $this->encrypter->decrypt($cookie);
	}

  protected function decryptArray(array $cookie) {
		$decrypted = array();
		foreach ($cookie as $key => $value) {
			$decrypted[$key] = $this->encrypter->decrypt($value);
		}

		return $decrypted;
	}

  protected function encrypt(Response $response) {
		foreach ($response->headers->getCookies() as $key => $c) {
			$encrypted = $this->encrypter->encrypt($c->getValue());
			$response->headers->setCookie($this->duplicate($c, $encrypted));
		}

		return $response;
	}

  protected function duplicate(Cookie $c, $value) {
		return new Cookie(
			$c->getName(), $value, $c->getExpiresTime(), $c->getPath(),
			$c->getDomain(), $c->isSecure(), $c->isHttpOnly()
		);
	}

}
