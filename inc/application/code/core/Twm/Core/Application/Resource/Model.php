<?php

class Twm_Core_Application_Resource_Model extends Zend_Application_Resource_ResourceAbstract {
    const ADAPTER_DB = 'database';
    const ADAPTER_MONGO = 'mongo';

    protected $_adapter = null;

    public function init() {
	foreach ($this->getOptions() as $key => $value) {
	    switch (strtolower($key)) {
		case 'adapter':
		    $this->_setAdapters($value);
		    break;
		default:
		    break;
	    }
	}

	Twm_Core_Model_Adapter_Abstract::setDefaultAdapter($this->_adapter);
	return $this;
    }

    protected function _setAdapters(array $options) {
	foreach ($options as $key => $value) {
	    switch (strtolower($key)) {
		case self::ADAPTER_DB:
		    $this->_adapter = $this->_getDbAdapter($value);
		    break;
		case self::ADAPTER_MONGO:
		    throw new Twm_Core_Exception('Mongo adapter not implemented yet');
		    break;
		default:
		    break;
	    }
	}
    }

    protected function _getDbAdapter(array $options) {
	if (null === $this->_adapter) {
	    $this->getBootstrap()->bootstrap('multidb');
	    if (isset($options['db'])) {
		$options = $options['db'];
		if (is_array($options)) {
		    $dbAdapter = Zend_Db::factory($options['adapter'], $options['params']);
		} else if (is_string($options)) {
		    $db = $options;
		    $this->getBootstrap()->bootstrap('multidb');
		    $multidb = $this->getBootstrap()->multidb;
		    $dbAdapter = $multidb->getDb($db);
		}
	    } else {
		$this->getBootstrap()->bootstrap('multidb');
		$multidb = $this->getBootstrap()->multidb;
		if (!$multidb) {
		    throw new Twm_Account_Exception('Unable to get default dbAdapter');
		}
		$dbAdapter = $multidb->getDb();
	    }

	    $this->_adapter = new Twm_Core_Model_Adapter_Database($dbAdapter);
	}
	return $this->_adapter;
    }

}