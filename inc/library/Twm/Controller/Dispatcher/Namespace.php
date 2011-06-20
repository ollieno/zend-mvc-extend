<?php

class Twm_Controller_Dispatcher_Namespace extends Zend_Controller_Dispatcher_Standard {

    protected $_controllerNamespace = array();
    protected $_controllerDirectoryOverride = array();

    public function addControllerDirectory($path, $module = null) {
	if (null === $module) {
	    $module = $this->_defaultModule;
	}

	$module = (string) $module;
	$module = strtolower($module);
	$path = rtrim((string) $path, '/\\');
	$path_a = dirname(dirname($path));
	$ns = substr($path_a, strlen(dirname($path_a)) + 1);

	if (!isset($this->_controllerDirectory[$module])) {
	    $this->_controllerNamespace[$module] = $ns;
	    $this->_controllerDirectory[$module] = $path;
	} else {
	    $this->_controllerDirectoryOverride[strtolower($ns)][strtolower($module)] = $path;
	}
	return $this;
    }

    /**
     * Return the currently set namespaces for Zend_Controller_Action class
     * lookup
     *
     * If a module is specified, returns just that namespace.
     *
     * @param  string $module Module name
     * @return array|string Returns array of all namespaces by default, single
     * module namespace if module argument provided
     */
    public function getModuleNamespace($module = null) {
	if (null === $module) {
	    return $this->_controllerNamespace;
	}

	$module = (string) $module;
	if (array_key_exists($module, $this->_controllerNamespace)) {
	    return $this->_controllerNamespace[$module];
	}

	return null;
    }

    /**
     * Return the currently set namespaces for Zend_Controller_Action class
     * lookup
     *
     * If a module is specified, returns just that namespace.
     *
     * @param  string $module Module name
     * @return array|string Returns array of all namespaces by default, single
     * module namespace if module argument provided
     */
    public function getControllerDirectoryOverride($namespace=null, $module=null) {
	$module = (string) strtolower($module);
	if (null === $namespace) {
	    return $this->_controllerDirectoryOverride;
	}
	$namespace = (string) strtolower($namespace);

	if (array_key_exists($namespace, $this->_controllerDirectoryOverride)) {

	    if (null === $module) {
		return $this->_controllerDirectoryOverride[$namespace];
	    }
	    if (isset($this->_controllerDirectoryOverride[$namespace][$module])) {
		return $this->_controllerDirectoryOverride[$namespace][$module];
	    }
	}
	return null;
    }

    function getControllerOverridePath($className) {
	list($namespace, $module, ) = explode('_', strtolower($className));
	if (isset($this->_controllerDirectoryOverride[$namespace][$module])) {
	    return $this->_controllerDirectoryOverride[$namespace][$module];
	}
	return false;
    }

    /**
     * Format action class name
     *
     * @param string $moduleName Name of the current module
     * @param string $className Name of the action class
     * @return string Formatted class name
     */
    public function formatClassName($moduleName, $className) {
	$ns = $this->_controllerNamespace[strtolower($moduleName)];
	$controllerDirectoryName = $this->getFrontController()->getModuleControllerDirectoryName();
	$class = $this->formatModuleName(ucfirst($ns) . '_' . ($moduleName) . '_' . $controllerDirectoryName . '_' . $className);
	return $class;
    }

    /**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className) {

	$finalClass = $className;
	if (($this->_defaultModule != $this->_curModule)
		|| $this->getParam('prefixDefaultModule')) {
	    $finalClass = $this->formatClassName($this->_curModule, $className);
	}
	if (class_exists($finalClass, false)) {
	    return $finalClass;
	}

	// check override
	$twm = $this->_invokeParams['bootstrap']->getOption('twm');
	if (isset($twm['dispatcher']['override']['controllers']['class'][strtolower($finalClass)])) {
	    $finalClass = $twm['dispatcher']['override']['controllers']['class'][strtolower($finalClass)];
	    $dispatchDir = $this->getControllerOverridePath($finalClass);
	} else {
	    $dispatchDir = $this->getDispatchDirectory();
	}
	// end check

	$loadFile = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);

	if (Zend_Loader::isReadable($loadFile)) {
	    include_once $loadFile;
	} else {
	    require_once 'Zend/Controller/Dispatcher/Exception.php';
	    throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $loadFile . "'");
	}

	if (!class_exists($finalClass, false)) {
	    require_once 'Zend/Controller/Dispatcher/Exception.php';
	    throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
	}

	return $finalClass;
    }

}