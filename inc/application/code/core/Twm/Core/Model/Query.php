<?php

class Twm_Core_Model_Query {

	public $modelClass = "";
	public $pageSize;
	public $offset;
	public $key;
	public $sort = array();
	public $attributes = array();
	public $filters = array();

	function __construct($className) {
		$this->modelClass = $className;
	}

	function setKey($key) {
		$this->key = $key;
		return $this;
	}

	function setPageSize($size) {
		$this->pageSize = $size;
		return $this;
	}

	function setOffset($offset) {
		$this->offset = $offset;
		return $this;
	}

	function setAttribute($k, $v) {
		throw new Exception('use addFilter');
		$this->attributes[$k] = $v;
		return $this;
	}

	function addSort($field, $direction)
	{
		$this->sort[$field]=$direction;
		return $this;
	}

	function addFilter($field, $value, $options = array())
	{
		$this->filters[] = new Twm_Core_Model_Query_Filter($field, $value, $options);
		return $this;
	}
}