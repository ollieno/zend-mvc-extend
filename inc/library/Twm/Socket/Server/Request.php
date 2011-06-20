<?php

class Twm_Socket_Server_Request {

	/**
	 * Requested method
	 * @var string
	 */
	protected $_method;
	/**
	 * Regex for method
	 * @var string
	 */
	protected $_methodRegex = '/^[a-z][a-z0-9_.]*$/i';
	/**
	 * Request parameters
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Set request state
	 *
	 * @param  array $options
	 * @return Twm_Socket_Server_Request
	 */
	public function setOptions(array $options) {
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	/**
	 * Add a parameter to the request
	 *
	 * @param  mixed $value
	 * @param  string $key
	 * @return Twm_Socket_Server_Request
	 */
	public function addParam($value, $key = null) {
		if ((null === $key) || !is_string($key)) {
			$index = count($this->_params);
			$this->_params[$index] = $value;
		} else {
			$this->_params[$key] = $value;
		}

		return $this;
	}

	/**
	 * Add many params
	 *
	 * @param  array $params
	 * @return Twm_Socket_Server_Request
	 */
	public function addParams(array $params) {
		foreach ($params as $key => $value) {
			$this->addParam($value, $key);
		}
		return $this;
	}

	/**
	 * Overwrite params
	 *
	 * @param  array $params
	 * @return Twm_Socket_Server_Request
	 */
	public function setParams(array $params) {
		$this->_params = array();
		return $this->addParams($params);
	}

	/**
	 * Retrieve param by index or key
	 *
	 * @param  int|string $index
	 * @return mixed|null Null when not found
	 */
	public function getParam($index) {
		if (array_key_exists($index, $this->_params)) {
			return $this->_params[$index];
		}

		return null;
	}

	/**
	 * Retrieve parameters
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->_params;
	}

	/**
	 * Set request method
	 *
	 * @param  string $name
	 * @return Twm_Socket_Server_Request
	 */
	public function setMethod($name) {
		//if (!preg_match($this->_methodRegex, $name)) {
		//	$this->_isMethodError = true;
		//} else {
			$this->_method = $name;
		//}
		return $this;
	}

	/**
	 * Get request method name
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->_method;
	}

	public function toArray()
	{
		return array(
			'method' => $this->getMethod(),
			'params' => $this->getParams()
		);
	}

	public function __toString() {
		return print_r($this->toArray(), true);
	}

}