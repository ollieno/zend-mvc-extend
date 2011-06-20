<?php

class Twm_Service_Solr_Search_Field {

	protected $_name;

	function __construct($name)
	{
		$this->_name = $name;
	}

	function getName()
	{
		return $this->_name;
	}
}