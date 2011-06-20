<?php

class Twm_Nosql_Mongo_Object implements Twm_Nosql_Mongo_Object_Interface, ArrayAccess, IteratorAggregate {

	protected $_data;
	protected $_isChanged = false;

	public function __construct($data) {
		$this->_data = $data;
	}

	function &__get($key) {
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
		}
		return null;
	}

	function __set($key, $value) {
		$this->_data[$key] = $value;

		$this->_isChanged = true;
	}

	public function getIterator() {
		return new ArrayIterator($this->_data);
	}

	public function offsetSet($offset, $value) {
		$this->_data[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	public function toArray() {
		return $this->_data;
	}
}