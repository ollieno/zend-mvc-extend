<?php

class Twm_Core_Design_Layout_Section extends Twm_Core_Model_Abstract {

	protected $_blocks = array();
	protected $_reference = array();

	function setBlock($block) {
		if (is_array($block)) {
			foreach ($block as $blockConfig) {
				$this->_blocks[] = Twm::getBlock($blockConfig['type'], $blockConfig);
			}
		} else {
			$this->_blocks[] = $block;
		}
	}

	function setReference($reference) {
		if (is_array($reference)) {
			$layout = Twm::getLayout();
			foreach ($reference as $referenceConfig) {
				$name = $referenceConfig['name'];
				if (null !== ($block = $layout->getBlock($name))) {
					$block->setConfig($referenceConfig);
				}
			}
		}
	}

}