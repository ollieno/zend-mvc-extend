<?php

class Twm_Service_Solr_Search_Facetmap_Facet_Filter {

	protected $_name;
	protected $_value;
	protected $_count;
	protected $_selected;

	function __construct($name, $value, $count) {
		$this->_name = $name;
		$this->_value = $value;
		$this->_count = $count;
	}

	function getName() {
		return $this->_name;
	}

	function getValue() {
		return $this->_value;
	}

	function getCount() {
		return $this->_count;
	}

	function isSelected() {
		if (null === $this->_selected) {
			$this->_selected = false;
			$query = Twm_Service_Solr_Search_Facetmap::$result->getQuery();
			$filterQueries = $query->getFilterQueries();
			foreach ($filterQueries as $fq) {
				list($name, $value) = explode(':', $fq);
				if ($this->_name == $name && $this->_value == $value) {
					$this->_selected = true;
				}
			}
		}
		return $this->_selected;
	}

	function getQuery() {
		return "{$this->getName()}:{$this->getValue()}";
	}

}