<?php

class Twm_Core_Model_Adapter_Database extends Twm_Core_Model_Adapter_Abstract {

	protected $_db = null;

	function __construct(Zend_Db_Adapter_Abstract $dbAdapter) {
		$this->_db = $dbAdapter;
	}

	function getDb() {
		return $this->_db;
	}

	protected function formatTableName($modelClass) {
		$tableName = strtolower($modelClass);
		$parts = explode('_', $tableName);
		unset($parts[0]);
		unset($parts[2]);
		return implode('_', $parts);
	}

	protected function _select(Twm_Core_Model_Query $query) {
		$tableName = $this->formatTableName($query->modelClass);
		$select = $this->_db->select()->from($tableName);
		if ($query->pageSize) {
			$select->limit($query->pageSize, $query->offset);
		}
		foreach ($query->sort as $field => $direction) {
			$select->order("{$field} {$direction}");
		}
		foreach ($query->filters as $filter) {
			$fieldName = $this->_db->quoteIdentifier($filter->getField());
			$select->where("$fieldName {$filter->getOperator()} ?", $filter->getValue());
		}
		return $select;
	}

	/**
	 *
	 * @param Twm_Core_Model_Query $query
	 * @return IteratorIterator
	 */
	function query(Twm_Core_Model_Query $query) {
		$select = $this->_select($query);
		$stmt = $this->_db->query($select);
		return $stmt->getIterator();
	}

	function count(Twm_Core_Model_Query $query) {
		$select = $this->_select($query);
		$select->reset(Zend_Db_Select::LIMIT_COUNT);
		$select->reset(Zend_Db_Select::LIMIT_OFFSET);
		$stmt = $this->_db->query($select);
		return $stmt->rowCount();
	}

	function get(Twm_Core_Model_Query $query) {
		$select = $this->_select($query);
		$stmt = $this->_db->query($select);
		return $stmt->fetch();
	}

	function save($data, $modelClass) {
		$tableName = $this->formatTableName($modelClass);
		$schema = $this->_db->describeTable($tableName);
		//d($schema);
		$columns = array_keys($schema);
		$primary = array();
		$id = array();
		foreach ($data as $k => $v) {
			if (!in_array($k, $columns)) {
				unset($data[$k]);
			} else {
				if ($schema[$k]['PRIMARY']) {
					if (!empty($data[$k])) {
						$primary[] = array($k, $v);
						$id[] = $v;
					}
				}
			}
		}
		if (count($primary) > 0) {
			$where = array();
			foreach ($primary as $key) {
				$where[] = $this->_db->quoteInto("{$key[0]} = ?", $key[1]);
			}
			$this->_db->update($tableName, $data, $where);
			return implode('-', $id);
		} else {
			$result = $this->_db->insert($tableName, $data);
			return $this->_db->lastInsertId();
		}
	}

	function remove($id, $key, $modelClass) {
		$tableName = $this->formatTableName($modelClass);
		$where = $this->_db->quoteInto("{$key} = ?", $id);
		return $this->_db->delete($tableName, $where);
	}

}