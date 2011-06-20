<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Nosql
 *
 * @author rob
 */
class Twm_Nosql {

	private static $_defaultDb;

	public static function setDefaultDb($db) {
		self::$_defaultDb = $db;
	}

	public static function getDefaultDb() {
		return self::$_defaultDb;
	}

	public static function factory($options) {
		switch (strtolower($options['type'])) {
			case 'mongo':
				$db = new Twm_Nosql_Mongo_Db($options['host'], $options['dbname']);
				self::setDefaultDb($db);
			break;
			default:
				throw new Twm_Exception('Unknow nosql type ' . $options['type']);
		}
	}

}
