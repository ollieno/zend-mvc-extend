<?php

class Twm_Service_Solr_Field {

	/**
	 * Field name
	 *
	 * @var string
	 */
	public $name;
	/**
	 * Field value
	 *
	 * @var boolean
	 */
	public $value;
	/**
	 * Field value encoding.
	 *
	 * @var string
	 */
	public $encoding;

	public function __construct($name, $value, $encoding='utf8') {
		$this->name = $name;
		$this->value = $value;
		$this->encoding = $encoding;
	}

	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	public function getUtf8Value() {
		if (strcasecmp($this->encoding, 'utf8') == 0 ||
			strcasecmp($this->encoding, 'utf-8') == 0) {
			return $this->value;
		} else {

			return (PHP_OS != 'AIX') ? iconv($this->encoding, 'UTF-8', $this->value) : iconv('ISO8859-1', 'UTF-8', $this->value);
		}
	}

}