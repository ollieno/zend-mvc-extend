<?php

class Twm_Core_Application_Resource_Modules extends Zend_Application_Resource_Modules {

    protected $_front;

    /**
     * Initialize modules
     *
     * @return array
     * @throws Zend_Application_Resource_Exception When bootstrap class was not found
     */
    public function init() {
	$bootstrap = $this->getBootstrap();
	$bootstrap->bootstrap('frontcontroller');
	$this->_front = $bootstrap->getResource('frontcontroller');

	$modules = $this->_front->getControllerDirectory();
	$this->load($bootstrap, $modules);

	$overrides = $this->_front->getDispatcher()->getControllerDirectoryOverride();
	foreach ($overrides as $namespace => $modules) {
	    $this->load($bootstrap, $modules, $namespace);
	}

	return $this->_bootstraps;
    }

    private function load($bootstrap, $modules, $namespace=null) {
	$default = $this->_front->getDefaultModule();
	$curBootstrapClass = get_class($bootstrap);
	foreach ($modules as $module => $moduleDirectory) {
	    // start add namespace prefix;
	    if (null === $namespace) {
		$namespace = $this->_front->getDispatcher()->getModuleNamespace($module);
	    }
	    $bootstrapClass = $this->_formatModuleName($namespace) . '_' . $this->_formatModuleName($module) . '_Bootstrap';
	    // end add namespace prefix;
	    if (!class_exists($bootstrapClass, false)) {
		$bootstrapPath = dirname($moduleDirectory) . '/Bootstrap.php';
		if (file_exists($bootstrapPath)) {
		    $eMsgTpl = 'Bootstrap file found for module "%s" but bootstrap class "%s" not found';
		    include_once $bootstrapPath;
		    if (($default != $module)
			    && !class_exists($bootstrapClass, false)
		    ) {
			throw new Zend_Application_Resource_Exception(sprintf(
					$eMsgTpl, $module, $bootstrapClass
			));
		    } elseif ($default == $module) {
			if (!class_exists($bootstrapClass, false)) {
			    $bootstrapClass = 'Bootstrap';
			    if (!class_exists($bootstrapClass, false)) {
				throw new Zend_Application_Resource_Exception(sprintf(
						$eMsgTpl, $module, $bootstrapClass
				));
			    }
			}
		    }
		} else {
		    continue;
		}
	    }

	    if ($bootstrapClass == $curBootstrapClass) {
		// If the found bootstrap class matches the one calling this
		// resource, don't re-execute.
		continue;
	    }
	    $moduleBootstrap = new $bootstrapClass($bootstrap);
	    //$moduleBootstrap->bootstrap();
	    $this->_bootstraps[$module] = $moduleBootstrap;
	}
	return $this->_bootstraps;
    }

}
