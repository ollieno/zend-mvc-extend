<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initModuleDispatcher() {
	$front = Zend_Controller_Front::getInstance();
	$dispatcher = new Twm_Controller_Dispatcher_Namespace();
	$front->setDispatcher($dispatcher);
    }

    protected function _initConfig() {
	$options = $this->getOptions();
	Zend_Registry::set('Twm_Config', $options);
    }

    /**
     * Initialize the cache
     *
     * @return Zend_Cache_Core
     */
    protected function _initCache() {
	// Cache options
	$frontendOptions = array(
	    'lifetime' => 1200, // Cache lifetime of 20 minutes
	    'automatic_serialization' => true,
	);
	$backendOptions = array(
	    'cache_dir' => DATA_PATH . '/cache/', // Directory where to put the cache files
	);
	// DON'T cache in a development environment
	if ('development' == APPLICATION_ENV) {
	    $frontendOptions['caching'] = false;
	} else {
	    // enable Plugin Loader Cache - see ZF reference chapter 30.4.4.
	    $classFileIncCache = $backendOptions['cache_dir'] . 'pluginLoaderCache.php';
	    if (file_exists($classFileIncCache)) {
		include_once $classFileIncCache;
	    }
	    Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
	}
	// Get a Zend_Cache_Core object
	$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

	// Set cache for Zend_Db_Table - see ZF reference chapter 15.5.12.
	Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

	//Zend_Registry::set('cache', $cache);
	// Return it, so that it can be stored by the bootstrap
	return $cache;
    }

}