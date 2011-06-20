<?php

class Twm_Core_Model_Collection_Abstract implements Iterator, Countable {

    protected $_modelClass = null;
    protected $_adapter = null;
    protected $_iterator = null;
    protected $_query = null;

    protected function prepare() {
	if (null === $this->_iterator) {
	    $this->_iterator = $this->_adapter->query($this->getQuery());
	}
    }

    /**
     *
     * @return Twm_Core_Model_Query
     */
    public function getQuery() {
	if (null === $this->_query) {
	    $this->_query = new Twm_Core_Model_Query($this->_modelClass);
	}
	return $this->_query;
    }

    public function current() {
	$this->prepare();
	$data = $this->_iterator->current();
	return Twm::getModel($this->_modelClass, $data);
    }

    public function next() {
	$this->prepare();
	$data = $this->_iterator->next();
	return Twm::getModel($this->_modelClass, $data);
    }

    public function key() {
	$this->prepare();
	return $this->_iterator->key();
    }

    public function valid() {
	$this->prepare();
	return $this->_iterator->valid();
    }

    public function rewind() {
	$this->prepare();
	$this->_iterator->rewind();
    }

    public function count() {
	$query = $this->getQuery();
	return $this->_adapter->count($query);
    }

    public function get() {
	$this->prepare();
	$data = $this->_adapter->get($this->getQuery());
	return Twm::getModel($this->_modelClass, $data);
    }

    public function getIterator() {
	$this->prepare();
	return $this->_iterator;
    }

    public function setIterator($iterator) {
	$this->_iterator = $iterator;
    }

    function setModelClass($className) {
	$this->_modelClass = $className;
    }

    function getAdapter() {
	return $this->_adapter;
    }

    function setAdapter(Twm_Core_Model_Adapter_Abstract $adapter) {
	$this->_adapter = $adapter;
	if (null !== $this->_modelClass) {
	    $this->_adapter->setModelClass($this->_modelClass);
	}
    }

    public function toArray() {
	$data = array();
	$i = 0;
	foreach ($this as $item) {
	    $data[] = $item->getData();
	}
	return $data;
    }

    public function toJson() {
	return Zend_Json_Encoder::encode($this->toArray());
    }

}