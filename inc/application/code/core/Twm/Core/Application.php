<?php

require_once 'Zend/Application.php';

class Twm_Core_Application extends Zend_Application {

    public function __construct($environment, $options = null) {
	$this->_environment = (string) $environment;

	require_once 'Zend/Loader/Autoloader.php';
	$this->_autoloader = Zend_Loader_Autoloader::getInstance();

	if (null !== $options) {
	    if (is_string($options)) {
		$options = $this->_loadConfig($options);
	    } elseif ($options instanceof Zend_Config) {
		$options = $options->toArray();
	    } elseif (!is_array($options)) {
		throw new Zend_Application_Exception('Invalid options provided; must be location of config file, a config object, or an array');
	    }

	    $options = $this->mergeOptions($options, $this->_loadModuleConfigs());
	    $this->setOptions($options);
	}
    }

    protected function _loadModuleConfigs() {
	$options = array();
	foreach (new DirectoryIterator(CORE_MODULE_PATH) as $nsfileInfo) {
	    if ($nsfileInfo->isDot() || substr($nsfileInfo->getFilename(), 0, 1) == '.' || !$nsfileInfo->isDir())
		continue;
	    foreach (new DirectoryIterator(CORE_MODULE_PATH . $nsfileInfo->getFilename()) as $moduleFileInfo) {
		if ($moduleFileInfo->isDot() || substr($moduleFileInfo->getFilename(), 0, 1) == '.' || !$moduleFileInfo->isDir())
		    continue;
		$moduleName = $moduleFileInfo->getFilename();
		$configFileName = strtolower($moduleName) . '.ini';
		$file = CORE_MODULE_PATH . $nsfileInfo->getFilename() . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $configFileName;
		if (file_exists($file)) {
		    $options = $this->mergeOptions($options, $this->_loadConfig($file));
		}
	    }
	}
	return $options;
    }

}