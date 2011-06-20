<?php

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Twm_Controller_Action_Helper_FlashVars extends Zend_Controller_Action_Helper_Abstract implements IteratorAggregate, Countable {

	/**
	 * $_vars - Vars from previous request
	 *
	 * @var array
	 */
	static protected $_vars = array();
	/**
	 * $_session - Zend_Session storage object
	 *
	 * @var Zend_Session
	 */
	static protected $_session = null;
	/**
	 * $_varAdded - Wether a message has been previously added
	 *
	 * @var boolean
	 */
	static protected $_varAdded = false;
	/**
	 * $_namespace - Instance namespace, default is 'default'
	 *
	 * @var string
	 */
	protected $_namespace = 'default';

	/**
	 * __construct() - Instance constructor, needed to get iterators, etc
	 *
	 * @param  string $namespace
	 * @return void
	 */
	public function __construct() {
		if (!self::$_session instanceof Zend_Session_Namespace) {
			self::$_session = new Zend_Session_Namespace($this->getName());
			foreach (self::$_session as $namespace => $vars) {
				self::$_vars[$namespace] = $vars;
				unset(self::$_session->{$namespace});
			}
		}
	}

	/**
	 * postDispatch() - runs after action is dispatched, in this
	 * case, it is resetting the namespace in case we have forwarded to a different
	 * action, Flashmessage will be 'clean' (default namespace)
	 *
	 * @return Zend_Controller_Action_Helper_FlashMessenger Provides a fluent interface
	 */
	public function postDispatch() {
		$this->resetNamespace();
		return $this;
	}

	/**
	 * setNamespace() - change the namespace messages are added to, useful for
	 * per action controller messaging between requests
	 *
	 * @param  string $namespace
	 * @return Zend_Controller_Action_Helper_FlashMessenger Provides a fluent interface
	 */
	public function setNamespace($namespace = 'default') {
		$this->_namespace = $namespace;
		return $this;
	}

	/**
	 * resetNamespace() - reset the namespace to the default
	 *
	 * @return Zend_Controller_Action_Helper_FlashMessenger Provides a fluent interface
	 */
	public function resetNamespace() {
		$this->setNamespace();
		return $this;
	}

	/**
	 * addVar() - Add a message to flash message
	 *
	 * @param  string $var
	 * @return Zend_Controller_Action_Helper_FlashMessenger Provides a fluent interface
	 */
	public function addVar($var, $name = false) {
		if (self::$_varAdded === false) {
			self::$_session->setExpirationHops(1, null, true);
		}

		if (!is_array(self::$_session->{$this->_namespace})) {
			self::$_session->{$this->_namespace} = array();
		}

		if ($name) {
			self::$_session->{$this->_namespace}[$name] = $var;
		} else {
			self::$_session->{$this->_namespace}[] = $var;
		}

		return $this;
	}

	/**
	 * hasVars() - Wether a specific namespace has messages
	 *
	 * @return boolean
	 */
	public function hasVars($name=false) {
		if ($name) {
			return isset(self::$_vars[$this->_namespace][$name]);
		}
		return isset(self::$_vars[$this->_namespace]);
	}

	/**
	 * getVars() - Get messages from a specific namespace
	 *
	 * @return array
	 */
	public function getVars($name=false, $default=false) {
		if ($name) {
			if ($this->hasVars($name)) {
				return self::$_vars[$this->_namespace][$name];
			}
			return $default;
		}
		return self::$_vars[$this->_namespace];
	}

	/**
	 * Clear all messages from the previous request & current namespace
	 *
	 * @return boolean True if messages were cleared, false if none existed
	 */
	public function clearVars() {
		if ($this->hasVars()) {
			unset(self::$_vars[$this->_namespace]);
			return true;
		}

		return false;
	}

	/**
	 * hasCurrentVars() - check to see if messages have been added to current
	 * namespace within this request
	 *
	 * @return boolean
	 */
	public function hasCurrentVars() {
		return isset(self::$_session->{$this->_namespace});
	}

	/**
	 * getCurrentVars() - get messages that have been added to the current
	 * namespace within this request
	 *
	 * @return array
	 */
	public function getCurrentVars() {
		if ($this->hasCurrentVars()) {
			return self::$_session->{$this->_namespace};
		}

		return array();
	}

	/**
	 * clear messages from the current request & current namespace
	 *
	 * @return boolean
	 */
	public function clearCurrentVars() {
		if ($this->hasCurrentVars()) {
			unset(self::$_session->{$this->_namespace});
			return true;
		}

		return false;
	}

	/**
	 * getIterator() - complete the IteratorAggregate interface, for iterating
	 *
	 * @return ArrayObject
	 */
	public function getIterator() {
		if ($this->hasVars()) {
			return new ArrayObject($this->getVars());
		}

		return new ArrayObject();
	}

	/**
	 * count() - Complete the countable interface
	 *
	 * @return int
	 */
	public function count() {
		if ($this->hasVars()) {
			return count($this->getVars());
		}

		return 0;
	}

	/**
	 * Strategy pattern: proxy to addVar()
	 *
	 * @param  string $var
	 * @return void
	 */
	public function direct($var, $name=false) {
		return $this->addVar($var, $name);
	}

}
