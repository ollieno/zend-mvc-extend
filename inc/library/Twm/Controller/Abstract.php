<?php

class Twm_Controller_Abstract extends Zend_Controller_Action {

	function preDispatch() {
		$r = $this->_request;
		$page_key = array($r->getModuleName(), $r->getControllerName(), $r->getActionName());
		$page_key = implode('_', $page_key);
		$layout = Twm::getDesign()->getLayout();
		$hasPage = $layout->hasPage($page_key);
		if ($hasPage) {
			$r->setParam('___PAGE___', $page_key);
		}

		$this->_initView($this->_request);
	}

	protected function _initView(Zend_Controller_Request_Abstract $request) {
		$Package = Twm::getDesign()->getPackage();
		$paths = $Package->getBasePaths();
		$moduleName = $request->getModuleName();
		$ds = DIRECTORY_SEPARATOR;

		$this->view->setScriptPath(null);
		foreach ($paths as $designPath) {
			$this->view->addScriptPath(strtolower($designPath . $ds . 'modules' . $ds . $moduleName . $ds));
		}
	}

}