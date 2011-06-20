<?php

class Twm_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer {

	function preDispatch() {
		$r = $this->getRequest();
		$pageKey = array($r->getModuleName(), $r->getControllerName(), $r->getActionName());
		$pageKey = implode('_', $pageKey);
		$layout = Twm::getDesign()->getLayout();
		if (true === $layout->hasPage($pageKey)) {
			$block = $layout->getRootBlock($pageKey);		
			$this->_actionController->block = $block;
			$this->_actionController->view->block = $block;
		}
	}

	function render($action = null, $name = null, $noController = null) {
		if (isset($this->_actionController->block)) {
			$this->renderBlock($this->_actionController->block);
		} else {
			parent::render($action, $name, $noController);
		}
	}

	public function renderBlock($Block, $name = null) {
		if (null === $name) {
			$name = $this->getResponseSegment();
		}
		$this->getResponse()->appendBody(
				$Block->toHtml(),
				$name
		);

		$this->setNoRender();
	}

}