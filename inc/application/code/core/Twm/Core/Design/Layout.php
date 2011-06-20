<?php

class Twm_Core_Design_Layout {

	protected $_adapter = null;
	protected $_layoutPath = array();
	protected $_config = array();
	protected $_blocks;
	protected $_build = array();

	public function setAdapter(Twm_Core_Design_Layout_Adapter_Abstract $Adapter) {
		$this->_adapter = $Adapter;
		return $this;
	}

	public function addBlock(Twm_Core_Block_Abstract $block) {
		$name = $block->getName();
		if (isset($this->_blocks[$name])) {
			throw new Twm_Core_Design_Layout_Exception("Block name [$name] not unique");
		}
		$this->_blocks[$block->getName()] = $block;
	}

	public function getBlock($name) {
		if (isset($this->_blocks[$name])) {
			return $this->_blocks[$name];
		}
		return null;
	}

	public function getSections()
	{
		$layoutConfig = $this->getLayoutConfig();
		return array_keys($layoutConfig);
	}

	protected function _loadSection($section) {
		$config = $this->getLayoutConfig($section);
		if ($config) {
			$section = new Twm_Core_Design_Layout_Section();
			if (isset($config['block'])) {
				$section->setBlock($config['block']);
			}
			if (isset($config['reference'])) {
				$section->setReference($config['reference']);
			}
			return $section;
		}
		throw new Twm_Core_Design_Layout_Exception("Section [{$section}] not found");
	}

	public function hasPage($section) {
		$config = $this->getLayoutConfig($section);
		return (null !== $config);
	}

	public function getLayoutConfig($section="") {
		if (empty($this->_config[$section]) && (null !== ($config = $this->_adapter->getLayoutConfig($section)))) {
			$this->_config[$section] = $config;
		}
		return $this->_config[$section];
	}

	function getRootBlock($key) {
		if (!isset($this->_build[$key])) {
			$section = $this->_loadSection($key);
			$this->_root = $this->_blocks['root'];
		}
		return $this->_root;
	}

}