<?php

class Twm_Nosql_Mongo_Cursor implements Iterator, Countable {

	protected $_result;
	protected $_collection;
	protected $_query;

	public function __construct(Twm_Nosql_Mongo_Collection $collection, $query) {
		$this->_collection = $collection;
		$this->_query = $query;
		$this->_result = $collection->find($query->getCriteria(), $query->getFields());
	}

	public function current() {
		$class = $this->_collection->getDocumentClass();
		return new $class($this->_result->current(), $this->_collection);
	}

	public function key() {
		return $this->_result->key();
	}

	public function next() {
		$this->_result->next();
		return $this;
	}

	public function rewind() {
		return $this->_result->rewind();
	}

	public function valid() {
		return $this->_result->valid();
	}

	public function count() {
		return $this->_result->count();
	}

	public function limit($limit) {
		$this->_result->limit($limit);
		return $this;
	}

	public function skip($skip) {
		$this->_result->skip($skip);
		return $this;
	}

	public function sort(array $fields) {
		$this->_result->sort($sort);
		return $this;
	}

	public function toArray() {
		$data = array();
		foreach($this as $row) {
			$data[] = $row->toArray();
		}
		return $data;
	}
}

