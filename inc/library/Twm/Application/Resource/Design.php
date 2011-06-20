<?php

class Twm_Application_Resource_Design extends Zend_Application_Resource_ResourceAbstract {

    function init() {
	$this->getBootstrap()->bootstrap('layout');

	foreach ($this->getOptions() as $key => $value) {
	    switch (strtolower($key)) {
		case 'packages':
		    $this->_initPackages($value);
		    break;
		default:
		    break;
	    }
	}
    }

    protected function _initPackages($options) {

	$Design = Twm::getDesign();

	foreach ($options as $key => $packageOptions) {
	    $Package = new Twm_Core_Design_Package($key, $packageOptions);
	    $Design->addPackage($Package);
	}
	$Design->rebuild();
    }

}