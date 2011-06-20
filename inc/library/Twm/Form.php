<?php

class Twm_Form extends Zend_Form {

	public function setValues($data) {
		foreach ($this->getElements() as $key => $element) {
			if (isset($data[$key])) {
				$element->setValue($data[$key]);
			}
		}
	}

}