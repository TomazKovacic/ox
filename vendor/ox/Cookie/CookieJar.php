<?php namespace ox\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

class CookieJar {

  protected $path = '/';
  protected $domain = null;
  protected $queued = array();

  public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
		list($path, $domain) = $this->getPathAndDomain($path, $domain);
		$time = ($minutes == 0) ? 0 : time() + ($minutes * 60);
		return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly);
	}

  public function forget($name) {
		return $this->make($name, null, -2628000);
	}

  public function hasQueued($key) {
		return ! is_null($this->queued($key));
	}

  public function queued($key, $default = null) {
		return array_get($this->queued, $key, $default);
	}

  public function queue() {
		if (head(func_get_args()) instanceof Cookie) {
			$cookie = head(func_get_args());
		} else {
			$cookie = call_user_func_array(array($this, 'make'), func_get_args());
		}
		$this->queued[$cookie->getName()] = $cookie;
	}

  public function unqueue($name) {
		unset($this->queued[$name]);
	}

  protected function getPathAndDomain($path, $domain) {
		return array($path ?: $this->path, $domain ?: $this->domain);
	}

  public function setDefaultPathAndDomain($path, $domain) {
		list($this->path, $this->domain) = array($path, $domain);
		return $this;
	}

  public function getQueuedCookies() {
		return $this->queued;
	}
}
