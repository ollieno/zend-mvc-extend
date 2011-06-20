<?php

class Twm_Validate_AgreementRequired extends Zend_Validate_Abstract {
	const NOT_AGREED = 'not_agreed';


	protected $_messageTemplates = array(
		self::NOT_AGREED => "Agreement required",
	);

	public function isValid($value) {
		$isValid = true;
		if ($value==0) {
			$isValid = false;
			$this->_error(self::NOT_AGREED);
		}
		return $isValid;
	}

}