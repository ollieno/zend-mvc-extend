<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Navigation.php 22882 2010-08-22 14:00:16Z freak $
 */
/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for setting navigation structure
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Dolf Schimmel
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Twm_Application_Resource_Navigation extends Zend_Application_Resource_Navigation {

	public function init() {
		if (!$this->_container) {
			$options = $this->getOptions();
			$frontend = (isset($options['frontend'])) ? $options['frontend'] : array();
			$backend = (isset($options['backend'])) ? $options['backend'] : array();
			$pages = isset($options['pages']) ? $options['pages'] : array();
			$this->_container['frontend'] = new Zend_Navigation($frontend);
			$this->_container['backend'] = new Zend_Navigation($backend);
			$this->_container['pages'] = new Zend_Navigation($pages);

			if (isset($options['defaultPageType'])) {
				Zend_Navigation_Page::setDefaultPageType($options['defaultPageType']);
			}
		}

		$this->store();
		return $this->_container;
	}

	/**
	 * Returns navigation container
	 *
	 * @return Zend_Navigation
	 */
	public function getContainer($area=false) {
		if (!$area) {
			return $this->_container;
		}
		if (isset($this->_container[$erea])) {
			return $this->_container[$erea];
		}
		throw new Zend_Navigation_Exception("container [{$area}] not found");
	}

}
