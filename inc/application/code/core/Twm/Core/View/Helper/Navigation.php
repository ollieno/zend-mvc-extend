<?php

class Twm_Core_View_Helper_Navigation extends Zend_View_Helper_Navigation {

	public function navigation($container = null) {

		if (null !== $container) {
			$this->_container = $container;
		}
		return $this;
	}

    public function getContainer()
    {
		$area = Twm::getDesign()->getArea();
        if (null === $this->_container) {
            // try to fetch from registry first
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Navigation')) {
                $nav = Zend_Registry::get('Zend_Navigation');
                if ($nav instanceof Zend_Navigation_Container) {
                    return $this->_container = $nav;
                }
            }

            // nothing found in registry, create new container
            require_once 'Zend/Navigation.php';
            return $this->_container = new Zend_Navigation();

        }

        return $this->_container[$area];
    }
}