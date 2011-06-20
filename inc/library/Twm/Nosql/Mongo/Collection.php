<?php

class Twm_Nosql_Mongo_Collection implements IteratorAggregate, Countable {

	protected $_db;
	protected $_collection;
	protected $_name;
	protected $_documentClass = 'Twm_Nosql_Mongo_Document';

	public function __construct($db = null, $name = null) {
		if (!is_null($name) && !is_string($name)) {
			throw new Twm_Nosql_Mongo_Exception('No valid collection name given');
		}

		$this->_db = $db;
		$this->_name = $name;
		$this->_collection = @$db->$name;
	}

	public function &__get($key) {
		$val = $this->getCursor()->key($key);
		return $val;
	}

	public function setOptions($options) {
		
	}

	public function setDocumentClass($className) {
		$this->_documentClass = $className;
	}

	public function getDocumentClass() {
		return $this->_documentClass;
	}

	public function ensureIndex($field, $direction) {
		$this->_collection->ensureIndex(array($field, $direction));
	}

	public function save($data) {
		$this->_collection->save($data);
	}

	public function update($object, $where=array()) {
		$data = (is_object($object))?$object->toArray():$object;
		return $this->_collection->update($where, $data);
	}

	public function insert($data) {
		$this->_collection->insert($data);
	}

	public function remove(array $query)
	{
		$this->_collection->remove($query, true);
	}

	public function query() {
		return new Twm_Nosql_Mongo_Query($this);
	}

	public function count() {
		return $this->_collection->count();
	}

	public function find(array $query, array $fields = array()) {
		return $this->_collection->find($query, $fields);
	}

	public function getCursor() {
		$query = new Twm_Nosql_Mongo_Query($this);
		return new Twm_Nosql_Mongo_Cursor($this, $query);
	}

	public function getIterator() {
		return $this->getCursor();
	}

	public function setFieldTypes($field, $types) {
		
	}

	public function setFieldsTypes($fieldstypes) {
		
	}

	public function setFieldValidator($field, $validator) {
		
	}

	public function getDb() {
		return $this->_db;
	}
}

