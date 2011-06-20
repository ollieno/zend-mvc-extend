<?php

class Twm_Core_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function initResourceLoader() {
	
    }

    protected function _initPluginloader() {
	$module = ucfirst($this->getModuleName());
	$namespace = ucfirst($this->getAppNamespace());

	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->registerNamespace($namespace);
    }

    public function getAppNamespace() {
	if (empty($this->_appNamespace)) {
	    $class = get_class($this);
	    if (preg_match('/^([a-z][a-z0-9]*)_/i', $class, $matches)) {
		$this->_appNamespace = $matches[1];
	    }
	}
	return $this->_appNamespace;
    }

    public function getModuleName() {
	if (empty($this->_moduleName)) {
	    $class = get_class($this);
	    if (preg_match('/^([a-z][a-z0-9]*)_([a-z][a-z0-9]*)/i', $class, $matches)) {
		$this->_appNamespace = $matches[1];
		$this->_moduleName = $matches[2];
	    }
	}
	return $this->_moduleName;
    }

}