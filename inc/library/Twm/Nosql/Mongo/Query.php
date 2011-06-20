<?php

require_once 'Twm/Nosql/Mongo/Query.php';

class Twm_Nosql_Mongo_Query implements IteratorAggregate, Countable {

	private $_limit = 0;

	private $_skip = 0;

	private $_sort;

	protected $_cursor;

	protected $_collection;

	protected $_criteria = array();

	protected $_fields = array();

	public function __construct(Twm_Nosql_Mongo_Collection $collection) {
		$this->_collection = $collection;
	}

	public function getCursor() {
		if (null === $this->_cursor) {
			$this->_cursor = new Twm_Nosql_Mongo_Cursor($this->_collection, $this);

			if ($this->_limit > 0) {
				$this->_cursor->limit($this->_limit);
			}
			if ($this->_skip > 0) {
				$this->_cursor->skip($this->_skip);
			}
			if (null !== $this->_sort) {
				$this->_cursor->sort($this->_sort);
			}
		}
		return $this->_cursor;
	}

	public function count() {
		return $this->_collection->count($this->_criteria, $this->_limit, $this->_skip);
	}

	public function getIterator() {
		$cursor = $this->getCursor();

		return $cursor;
	}

	public function addField($field) {
		if (is_array($field)) {
			array_merge($this->_fields, $field);
		}
		$this->_fields[] = $field;
		return $this;
	}

	public function field($field) {
		return $this->addField($field);
	}

	public function getFields() {
		return $this->_fields;
	}

	public function getCriteria() {
		return $this->_criteria;
	}

	public function where($key, $val=false) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->_criteria[$k] = $v;
			}
		} else {
			$this->_criteria[$key] = $val;
		}
		return $this;
	}

	public function limit($limit) {
		if (!is_numeric($limit)) {
			throw new Zend_Nosql_Mongo_Exception('Limit must be a number');
		}
		$this->_limit = $limit;
		return $this;
	}

	public function skip($skip) {
		if (!is_numeric($skip)) {
			throw new Zend_Nosql_Mongo_Exception('Skip must be a number');
		}
		$this->_skip = $skip;
		return $this;
	}

	public function sort(array $fields) {
		$this->_fields = $fields;
		return $this;
	}

	public function toArray() {
		return $this->getCursor()->toArray();
	}

	public function toJson() {
		return Zend_Json::encode($this->toArray());
	}
}

