<?php

class Twm_Core_Design {

	protected $_area = 'frontend';
	protected $_packages = array();
	protected $_layout = null;
	protected $_activePackage = array();

	function addPackage(Twm_Core_Design_Package $Package) {
		$area = $Package->getArea();
		$this->_packages[$area][$Package->getKey()] = $Package;
		if ($Package->isActive()) {
			$this->_activePackage[$area] = $Package;
		}
	}

	/**
	 *
	 * @return Twm_Core_Design_Package
	 */
	function getPackage() {
		$area = $this->getArea();
		if (isset($this->_activePackage[$area])) {
			return $this->_activePackage[$area];
		}
		throw new Twm_Core_Design_Exception("No active design package set");
	}

	function getArea() {
		return $this->_area;
	}

	function setArea($area) {
		$this->_area = $area;
		return $this;
	}

	/**
	 *
	 * @return Twm_Core_Design_Layout
	 */
	function getLayout() {
		if (null === $this->_layout) {
			$layout = Twm::getLayout();
			$this->_layout = $layout;
		}
		return $this->_layout;
	}

	function rebuild() {
		foreach ($this->_activePackage as $area => $Package) {
			$extendKey = $Package->getExtendKey();
			while ((bool) $extendKey) {
				$ParentPackage = $this->_packages[$area][$extendKey];
				$Package->setParent($ParentPackage);
				$extendKey = $ParentPackage->getExtendKey();
				$Package = $ParentPackage;
			}
		}
	}

}
