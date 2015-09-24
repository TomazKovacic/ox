<?php namespace ox\framework;

/** 
* ORIGINAL Symfony\Component\HttpFoundation\ParameterBag
*/

class ParameterBag implements \IteratorAggregate, \Countable {
	
	protected $parameters;
	
    public function __construct(array $parameters = array()) {
        $this->parameters = $parameters;
    }

    public function getIterator() {
        return new \ArrayIterator($this->parameters);
    }
	
    public function count() {
        return count($this->parameters);
    }
	
    public function all() {
        return $this->parameters;
    }

    public function keys() {
        return array_keys($this->parameters);
    }

    public function replace(array $parameters = array()) {
        $this->parameters = $parameters;
    }

    public function add(array $parameters = array()) {
        $this->parameters = array_replace($this->parameters, $parameters);
    }
	
	/* deep removed, simplified (try) */
	
	public function get($path, $default = null) {
		
		return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
	}
	
    public function set($key, $value) {
        $this->parameters[$key] = $value;
    }
	
    public function has($key) {
        return array_key_exists($key, $this->parameters);
    }

    public function remove($key) {
        unset($this->parameters[$key]);
    }
	
    public function getAlpha($key, $default = '') {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }
	
    public function getAlnum($key, $default = '') {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }
	
    public function getDigits($key, $default = '') {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(array('-', '+'), '', $this->filter($key, $default, FILTER_SANITIZE_NUMBER_INT));
    }

    public function getInt($key, $default = 0) {
        return (int) $this->get($key, $default);
    }

    public function filter($key, $default = null, $filter = FILTER_DEFAULT, $options = array())
    {
        $value = $this->get($key, $default);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!is_array($options) && $options) {
            $options = array('flags' => $options);
        }

        // Add a convenience check for arrays.
        if (is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }

        return filter_var($value, $filter, $options);
    }
}
