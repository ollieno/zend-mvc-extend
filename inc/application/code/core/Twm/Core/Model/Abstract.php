<?php

class Twm_Core_Model_Abstract extends Twm_Object {

	protected $_class;
	protected $_adapter;

	/**
	 * @return Twm_Core_Model_Collection_Model 
	 */
	public function getCollection() {
		$collection = new Twm_Core_Model_Collection_Model();
		$collection->setAdapter(Twm_Core_Model_Adapter_Abstract::getDefaultAdapter());
		$collection->setModelClass($this->_class);
		return $collection;
	}

	public function getAdapter() {
		if (null === $this->_adapter) {
			$this->_adapter = Twm_Core_Model_Adapter_Abstract::getDefaultAdapter();
		}
		return $this->_adapter;
	}

	public function load($id) {
		if ($id > 0) {
			$adapter = $this->getAdapter();
			$query = new Twm_Core_Model_Query($this->_class);
			$query->addFilter($this->_key, $id);
			$data = $adapter->get($query);
			$this->setData($data);
		}
	}

	public function save() {
		$adapter = $this->getAdapter();
		return $adapter->save($this->getData(), $this->_class);
	}

	public function remove()
	{
		$adapter = $this->getAdapter();
		$adapter->remove($this->getData($this->_key), $this->_key, $this->_class);
	}
}