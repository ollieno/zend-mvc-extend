<?php

class Twm_Object implements ArrayAccess {

	protected $_class;
	protected $_key;
	protected $_data = array();
	protected $_adapter;
	protected static $_underscoreCache = array();
	protected static $_camelizeCache = array();

	/**
	 * Constructor
	 *
	 * By default is looking for first argument as array and assignes it as object attributes
	 * This behaviour may change in child classes
	 *
	 */
	public function __construct() {
		$args = func_get_args();
		if (empty($args[0])) {
			$args[0] = array();
		}
		$this->setData($args[0]);
		$this->init($args[0]);
	}

	protected function init($data) {

	}

	/**
	 * Retrieves data from the object
	 *
	 * If $key is empty will return all the data as an array
	 * Otherwise it will return value of the attribute specified by $key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key = '', $index = null) {
		if ('' === $key) {
			return $this->_data;
		}

		$default = null;

		// legacy functionality for $index
		if (isset($this->_data [$key])) {
			return $this->_data [$key];
		}

		return $default;
	}

	/**
	 * Overwrite data in the object.
	 *
	 * $key can be string or array.
	 * If $key is string, the attribute value will be overwritten by $value
	 *
	 * If $key is an array, it will overwrite all the data in the object.
	 *
	 * $isChanged will specify if the object needs to be saved after an update.
	 *
	 * @param string|array $key
	 * @param mixed $value
	 * @param boolean $isChanged
	 * @return Admin_Model_Abstract
	 */
	public function setData($key, $value = null, $useFuncionCall=false) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$function = 'set' . $this->_camelize($k);
				$this->$function($v);
			}
		} else {
			if ($useFuncionCall) {
				$function = 'set' . $this->_camelize($key);
				$this->$function($value);
			} else {
				$this->_data[$key] = $value;
			}
		}
		return $this;
	}

	/**
	 * Unset data from the object.
	 *
	 * $key can be a string only. Array will be ignored.
	 *
	 * $isChanged will specify if the object needs to be saved after an update.
	 *
	 * @param string $key
	 * @param boolean $isChanged
	 * @return Admin_Model_Abstract
	 */
	public function unsetData($key = null) {
		if (is_null($key)) {
			$this->_data = array();
		} else {
			unset($this->_data [$key]);
		}
		return $this;
	}

	/**
	 * Converts field names for setters and geters
	 *
	 * $this->setMyField($value) === $this->setData('my_field', $value)
	 * Uses cache to eliminate unneccessary preg_replace
	 *
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name) {
		if ($name == "_") {
			return $name;
		}
		if (isset(self::$_underscoreCache [$name])) {
			return self::$_underscoreCache [$name];
		}
		$result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
		self::$_underscoreCache [$name] = $result;
		return $result;
	}

	/**
	 * Tiny function to enhance functionality of ucwords
	 *
	 * Will capitalize first letters and convert separators if needed
	 *
	 * @param string $str
	 * @param string $destSep
	 * @param string $srcSep
	 * @return string
	 */
	function uc_words($str, $destSep='_', $srcSep='_') {
		if ($str == "_") {
			return $str;
		}

		return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
	}

	protected function _camelize($name) {
		return $this->uc_words($name, '');
	}

	/**
	 * Set/Get attribute wrapper
	 *
	 * @param   string $method
	 * @param   array $args
	 * @return  mixed
	 */
	public function __call($method, $args) {
		switch (substr($method, 0, 3)) {
			case 'get' :
				$key = $this->_underscore(substr($method, 3));
				$data = $this->getData($key, isset($args [0]) ? $args [0] : null);
				return $data;
			case 'set' :
				$key = $this->_underscore(substr($method, 3));
				$result = $this->setData($key, isset($args [0]) ? $args [0] : null, false);
				return $result;

			case 'uns' :
				$key = $this->_underscore(substr($method, 3));
				$result = $this->unsetData($key);
				return $result;
			case 'has' :
				$key = $this->_underscore(substr($method, 3));
				return isset($this->_data [$key]);
			case substr($method, 0, 2) == 'is' :
				$key = $this->_underscore(substr($method, 2));
				if (count($args) == 0) {
					return (isset($this->_data [$key])) ? $this->getData($key) : false;
				} else {
					return $this->setData($key, $args[0]);
				}
			case substr($method, 0, 7) == 'iterate' :
				$key = $this->_underscore(substr($method, 7));
				$data = $this->getData($key, isset($args [0]) ? $args [0] : null);
				if (!$data) {
					return array();
				}
				if (!is_array($data)) {
					$data = array($data);
				}
				return $data;
		}
		throw new Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
	}

	/**
	 * Attribute getter (deprecated)
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var) {
		$var = $this->_underscore($var);
		return $this->getData($var);
	}

	/**
	 * Attribute setter (deprecated)
	 *
	 * @param string $var
	 * @param mixed $value
	 */
	public function __set($var, $value) {
		$var = $this->_underscore($var);
		$this->setData($var, $value);
	}

	/**
	 * ArrayAccess implementation
	 */
	public function offsetExists($property) {
		return isset($this->$property);
	}

	public function offsetSet($property, $value) {
		$this->$property = $value;
	}

	public function offsetGet($property) {
		return $this->$property;
	}

	public function offsetUnset($property) {
		unset($this->$property);
	}

	public function setClassName($className) {
		$this->_class = $className;
	}

	/**
	 * @return void
	 */
	public function dump() {
		Zend_Debug::dump($this);
	}

	public function toArray() {
		return new ArrayIterator($this);
	}

}