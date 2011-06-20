<?php

require_once 'Zend/View/Helper/Placeholder/Registry.php';
require_once 'Zend/View/Helper/Abstract.php';

class Twm_Core_View_Helper_Block extends Zend_View_Helper_Placeholder {

	/**
	 * Placeholder helper
	 *
	 * @param  string $name
	 * @return Zend_View_Helper_Placeholder_Container_Abstract
	 */
	public function block($name="") {
		$name = (string) $name;

		if (empty($name) && (null !== ($block = $this->view->block))) {
			$blocks = $block->getBlocks();
			$this->view->placeholder($block->getName())->set('');
			foreach ($blocks as $child) {
				$this->view->placeholder($block->getName())->append($child->toHtml());
			}
			return $this->_registry->getContainer($block->getName());
		} elseif (null !== ($block = $this->getBlock($name))) {

			$parent = $this->view->block;
			$parentName = ($parent)?$parent->getName() : '__PAGE__';

			$this->view->placeholder($parentName)->set('');
			$this->view->placeholder($parentName)->append($block->toHtml());
			return $this->_registry->getContainer($parentName);
		}
		return "";
	}

	protected function getBlock($name) {
		$Layout = Twm::getDesign()->getLayout();
		return $Layout->getBlock($name);
	}

}