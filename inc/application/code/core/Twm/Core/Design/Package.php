<?php

class Twm_Core_Design_Package {

	const DEFAULT_AREA = 'frontend';
	const DEFAULT_PACKAGE = 'default';
	const DEFAULT_THEME = 'default';
	const BASE_PACKAGE = 'base';

	/**
	 * Package area
	 *
	 * @var string
	 */
	protected $_skindir = 'skin';

	/**
	 * Package area
	 *
	 * @var string
	 */
	protected $_area;
	/**
	 * Package name
	 *
	 * @var string
	 */
	protected $_name;
	/**
	 * Package theme
	 *
	 * @var string
	 */
	protected $_theme;
	/**
	 * Parent Package
	 *
	 * @var Twm_Core_Design_Package
	 */
	protected $_parent = null;
	/**
	 * Package active
	 * @var bool
	 */
	protected $_active = false;

	/**
	 * Key to reference parent package
	 * 
	 * @var string
	 */
	protected $_extendKey;


	public function __construct($key, $options) {
		$this->setKey($key);
		foreach ($options as $key => $value) {
			switch (strtolower($key)) {
				case 'skindir':
					$this->setSkinDirectory($value);
					break;
				case 'area':
					$this->setArea($value);
					break;
				case 'name':
					$this->setName($value);
					break;
				case 'theme':
					$this->setTheme($value);
					break;
				case 'active':
					$this->setActive($value);
					break;
				case 'extend':
					$this->setExtendKey($value);
					break;
				default:
					break;
			}
		}
	}

	public function setSkinDirectory($name)
	{
		$this->_skindir = $name;
	}

	/**
	 * Set the package key
	 * the is key is used to reference a extend package
	 *
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->_key = $key;
	}

	/**
	 * Get the package key
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * Set package name
	 *
	 * @param  string $name
	 * @return Twm_Core_Model_Design_Package
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * Retrieve package name
	 *
	 * @return string
	 */
	public function getName() {
		if (null === $this->_name) {
			$this->_name = self::DEFAULT_PACKAGE;
		}
		return $this->_name;
	}

	/**
	 * Set package area
	 *
	 * @param  string $area
	 * @return Twm_Core_Model_Design_Package
	 */
	public function setArea($area) {
		$this->_area = $area;
		return $this;
	}

	/**
	 * Retrieve package area
	 *
	 * @return string
	 */
	public function getArea() {
		if (null === $this->_area) {
			$this->_area = self::DEFAULT_AREA;
		}
		return $this->_area;
	}

	/**
	 * Set package theme
	 *
	 * @param  string $theme
	 * @return Mage_Core_Model_Design_Package
	 */
	public function setTheme($theme) {
		$this->_theme = $theme;
		return $this;
	}

	/**
	 * Retrieve package theme
	 *
	 * @return string
	 */
	public function getTheme() {
		if (null === $this->_theme) {
			$this->_theme = self::DEFAULT_THEME;
		}
		return $this->_theme;
	}

	public function getBasePaths() {
		$paths = array();
		$ds = DIRECTORY_SEPARATOR;
		$paths[] = DESIGN_PATH . $ds . $this->getArea() . $ds . $this->getName() . $ds . $this->getTheme();
		if (null !== $this->_parent) {

			$parentpaths = $this->getParent()->getBasePaths();
			$paths = array_merge($parentpaths, $paths);
		}
		return $paths;
	}

	public function setParent(Twm_Core_Design_Package $Package) {
		$this->_parent = $Package;
	}

	/**
	 * Get parent package
	 *
	 * @return Twm_Core_Design_Package
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * set if the package is active
	 * This doesn't actually makes the package active.
	 * This just indicates that is is active
	 * if switched active package at runtime
	 * make sure to rebuild the design
	 *
	 * @param bool $bool
	 */
	public function setActive($bool)
	{
		$this->_active = (bool)$bool;
	}

	/**
	 * Get active state
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->_active;
	}

	/**
	 * set the extend from package key.
	 * This doesn't actually makes the package extend.
	 * This just indicates that it wants to extends
	 * from a certain package. if switched active package
	 * at runtime make sure to rebuild the design
	 *
	 * @param string $packageKey
	 */
	public function setExtendKey($packageKey)
	{
		$this->_extendKey = $packageKey;
	}

	public function getExtendKey()
	{
		return $this->_extendKey;
	}

	public function getSkinUrl()
	{
		$parts = array();
		$parts[]=$this->_skindir;
		$parts[]=$this->_area;
		$parts[]=$this->_name;
		$parts[]=$this->_theme;
		return implode(DIRECTORY_SEPARATOR, $parts);
	}
}