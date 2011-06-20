<?php

abstract class Twm_Core_Model_Adapter_Abstract {

	protected static $_defaultAdapter = null;

	public static function setDefaultAdapter(Twm_Core_Model_Adapter_Abstract $adapter) {
		self::$_defaultAdapter = $adapter;
	}

	public static function getDefaultAdapter() {
		return self::$_defaultAdapter;
	}

	abstract function query(Twm_Core_Model_Query $query);
	abstract function count(Twm_Core_Model_Query $query);
	abstract function save($data, $modelClass);
	abstract function get(Twm_Core_Model_Query $query);
	abstract function remove($id, $key, $modelClass);

}