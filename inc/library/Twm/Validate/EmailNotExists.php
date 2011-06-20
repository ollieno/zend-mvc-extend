<?php
class Twm_Validate_EmailNotExists extends Zend_Validate_Db_RecordExists {
	const EMAIL_NOT_EXISTS = 'emailNotExists';

	protected $_messageTemplates = array(
		self::EMAIL_NOT_EXISTS => "Email '%value%' is geen lid van het netwerk."
	);

	public function isValid($value) {
		$valid = parent::isValid($value);
		if ($valid === false) {
			$this->_error(self::EMAIL_NOT_EXISTS);
		}
		return $valid;
	}

}