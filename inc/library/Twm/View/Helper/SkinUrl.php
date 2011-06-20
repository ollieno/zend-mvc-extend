<?php

require_once 'Zend/View/Helper/Placeholder/Registry.php';
require_once 'Zend/View/Helper/Abstract.php';

class Twm_View_Helper_SkinUrl extends Zend_View_Helper_Abstract {

    /**
     * Returns correct skin url
     *
     * $file is appended to the skin url for simplicity
     *
     * @param  string|null $file
     * @return string
     */
    public function skinUrl($file = null, $prependBaseUrl = true) {
	// Get baseUrl
	$skinUrl = $this->getSkinUrl($prependBaseUrl);

	// Remove trailing slashes
	if (null !== $file) {
	    $file = '/' . ltrim($file, '/\\');
	}

	return $skinUrl . $file;
    }

    protected function getSkinUrl($prependBaseUrl) {
	$package = Twm::getDesign()->getPackage();
	if ($prependBaseUrl) {
	    return $this->view->baseUrl($package->getSkinUrl());
	}
	return $package->getSkinUrl();
    }

}