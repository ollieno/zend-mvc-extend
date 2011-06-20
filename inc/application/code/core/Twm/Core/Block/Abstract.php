<?php

abstract class Twm_Core_Block_Abstract extends Twm_Object {

	protected $_view = null;
	protected $_layout = null;
	protected $_blocks = array();
	protected $_request;
	protected $_response;

	function init($data) {
		$front = Zend_Controller_Front::getInstance();
		if ($front) {
			$this->setRequest($front->getRequest());
			$this->setResponse($front->getResponse());
		}
		parent::init($data);
	}

	function getRequest() {
		if (null === $this->_request) {
			$front = Zend_Controller_Front::getInstance();
			if ($front) {
				$this->setRequest($front->getRequest());
			}
		}
		return $this->_request;
	}
	function getResponse() {
		if (null === $this->_response) {
			$front = Zend_Controller_Front::getInstance();
			if ($front) {
				$this->setResponse($front->getResponse());
			}
		}
		return $this->_response;
	}

	function setBlock($block) {
		if (is_array($block)) {
			foreach ($block as $blockConfig) {
				$block = Twm::getBlock($blockConfig['type'], $blockConfig);
				$this->addBlock($block);
			}
		} else {
			$this->addBlock($block);
		}
	}

	function addBlock(Twm_Core_Block_Abstract $block) {
		$block->setParent($this);
		$this->_blocks[$block->getName()] = $block;
	}

	function removeBlock($name) {
		if (isset($this->_blocks[$name])) {
			unset($this->_blocks[$name]);
		}
	}

	function getBlocks() {
		return $this->_blocks;
	}

	function setRequest(Zend_Controller_Request_Abstract $request) {
		$this->_request = $request;
	}

	function setResponse(Zend_Controller_Response_Abstract $response) {
		$this->_response = $response;
	}

	function setLayout(Twm_Core_Design_Layout $layout) {
		$this->_layout = $layout;
	}

	function getLayout() {
		return $this->_layout;
	}

	function setConfig($blockConfig) {
		$this->setData($blockConfig);
	}

	function setAction($actionConfig) {
		if (is_array($actionConfig)) {
			foreach ($actionConfig as $config) {
				$method = $config['method'];
				unset($config['method']);
				call_user_func_array(array($this, $method), $config);
			}
		}
	}

	function getView() {
		if (null === $this->_view) {
			$Package = Twm::getDesign()->getPackage();

			$paths = $Package->getBasePaths();
			$ds = DIRECTORY_SEPARATOR;

			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$view->setScriptPath(null);
			foreach ($paths as $designPath) {
				$view->addScriptPath(strtolower($designPath . $ds . 'modules' . $ds));
			}
			$this->_view = $view;
		}
		return $this->_view;
	}

	public function getChildHtml($name="") {
		$helper = new Zend_View_Helper_Placeholder();
		$helper->placeholder($this->getName())->set('');
		if (!empty($name)) {
			if (isset($this->_blocks[$name])) {
				$block = $this->_blocks[$name];
				$helper->placeholder($this->getName())->append($block->toHtml());
			}
		} else {
			$helper = new Zend_View_Helper_Placeholder();
			foreach ($this->getBlocks() as $block) {
				$helper->placeholder($this->getName())->append($block->toHtml());
			}
		}
		return $helper->getRegistry()->getContainer($this->getName());
	}

	public function toHtml() {
		return '';
	}

	public function debug() {
		foreach ($this->_blocks as $block) {
			echo $block->getName() . "<br />";
		}
	}

}