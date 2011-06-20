<?php

class Twm_Validate_UsernameUnique extends Zend_Validate_Db_NoRecordExists {
	const USERNAME_NOT_UNIQUE = 'usernameNotUnique';

	protected $_messageTemplates = array(
		self::USERNAME_NOT_UNIQUE => "Username '%value%' is allready registered."
	);

	public function isValid($value) {
		$valid = parent::isValid($value);
		if ($valid === false) {
			$this->_error(self::USERNAME_NOT_UNIQUE);
		}
		return $valid;
	}

}