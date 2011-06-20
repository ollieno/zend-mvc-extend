<?php

class Twm_Nosql_Mongo_Db extends Zend_Db_Adapter_Abstract {

	protected $_connection;

	protected $_mongo;

	protected $_dbName;

	protected $_config;

	public function __construct($config) {

		if (is_null($config['host'])) {
			$config['host'] = 'localhost';
		}

		if (is_null($config['dbname']) || empty($config['dbname'])) {
			throw new Twm_Nosql_Mongo_Exception('The dbname can not be null or empty');
		}

		$this->_dbName = $config['dbname'];

		$this->_config = $config;

		$this->_connect();
	}

	public function setOptions($options) {
		
	}

	public function listTables() {
		return $this->_connection->listCollections();
	}

	public function describeTable($tableName, $schemaName = null) {
		return array();
	}

	protected function _connect() {
		$this->_mongo = new Mongo($this->_config['host']);
		$this->_connection = $this->_mongo->selectDB($this->_dbName);
	}

	public function isConnected() {
		return is_resource($this->_connection);
	}

	public function closeConnection() {
		$this->_mongo->close();
	}

	public function prepare($sql) {
		return new Zend_Db_Statement($this, $sql);
	}

	public function lastInsertId($tableName = null, $primaryKey = null){
		return 0;
	}

	protected function _beginTransaction() {
		// no transaction db
	}

	protected function _commit() {
		// no transaction db
	}

	protected function _rollBack() {
		// no transaction db
	}

	public function setFetchMode($mode) {
		// always object
	}

	public function limit($sql, $count, $offset = 0) {
		return $sql;
	}

	public function supportsParameters($type) {
		return false;
	}

	public function getServerVersion() {
		return Mongo::VERSION;
	}

	public function getCollection($name) {
		$collection = new Twm_Nosql_Mongo_Collection($this->_connection, $name);
		return $collection;
	}


}

