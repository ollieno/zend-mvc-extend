<?php

require_once 'Twm/Nosql/Mongo/Document/Interface.php';
require_once 'Twm/Nosql/Mongo/Object.php';

class Twm_Nosql_Mongo_Document extends Twm_Nosql_Mongo_Object implements Twm_Nosql_Mongo_Document_Interface {

	private $_id;
	protected $_collection;

	public function __construct($data, Twm_Nosql_Mongo_Collection $collection) {
		$this->_collection = $collection;
		if (isset($data['_id'])) {
			$this->_id = $data['_id'];
			unset ($data['_id']);
		}

		parent::__construct($data);
	}

	public function getId() {
		if (null !== $this->_id) {
			return $this->_id;
		}
		return null;
	}

	public function getCollection() {
		return $this->_collection;
	}

	public function save() {
		$this->_collection->save($this);
	}

	public function isChanged() {
		return $this->_isChanged;
	}

	public function commit() {
		$this->save();
	}

	public function toArray() {
		$data = parent::toArray();
		if (null !== $this->getId()) {
			$data['id'] = $this->getId()->__toString();
		}
		return $data;
	}

}

