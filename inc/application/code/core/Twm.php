<?php

final class Twm {

	static private $_registry = array();

	/**
	 * Get design package
	 *
	 * @return Twm_Core_Design
	 */
	public static function getDesign() {
		return self::getSingleton('Twm_Core_Design');
	}

	/**
	 * Get layout
	 *
	 * @return Twm_Core_Design_Layout
	 */
	public static function getLayout() {
		return self::getSingleton('Twm_Core_Design_Layout');
	}

	public static function getService($className, array $arguments=array()) {
		return self::getSingleton($className, $arguments);
	}

	public static function getBlock($type, $arguments) {
		$block = self::getModel($type, $arguments);
		$layout = self::getLayout();
		$layout->addBlock($block);
		return $block;
	}

	/**
	 * Retrieve a value from registry by a key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public static function registry($key) {
		if (isset(self::$_registry[$key])) {
			return self::$_registry[$key];
		}
		return null;
	}

	/**
	 * Retrieve model object singleton
	 *
	 * @param   string $modelClass
	 * @param   array $arguments
	 * @return  Mage_Core_Model_Abstract
	 */
	public static function getSingleton($className='', array $arguments=array()) {
		$registryKey = '_singleton/' . $className;
		if (!self::registry($registryKey)) {
			self::register($registryKey, self::getClass($className, $arguments));
		}
		return self::registry($registryKey);
	}

	/**
	 *
	 * @param string $className
	 * @return Twm_Core_Model_Collection_Abstract
	 */
	public static function getCollection($className) {
		$model = self::getModel($className);
		return $model->getCollection();
	}

	/**
	 *
	 * @param string $className
	 * @param array $constructArguments
	 * @return Twm_Core_Model_Abstract
	 */
	public static function getModel($className='', $constructArguments=array()) {
		$model = self::getClass($className, $constructArguments);
		$model->setClassName($className);
		return $model;
	}

	public static function getClass($className='', $constructArguments=array()) {
		return new $className($constructArguments);
	}

	/**
	 * Register a new variable
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param bool $graceful
	 * @throws Mage_Core_Exception
	 */
	public static function register($key, $value, $graceful = false) {
		if (isset(self::$_registry[$key])) {
			if ($graceful) {
				return;
			}
			self::throwException('Mage registry key "' . $key . '" already exists');
		}
		self::$_registry[$key] = $value;
	}

	/**
	 * Unregister a variable from register by key
	 *
	 * @param string $key
	 */
	public static function unregister($key) {
		if (isset(self::$_registry[$key])) {
			if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
				self::$_registry[$key]->__destruct();
			}
			unset(self::$_registry[$key]);
		}
	}

}