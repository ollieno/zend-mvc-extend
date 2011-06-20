<?php

class Twm_Core_Model_Query_Filter {

	protected $_field;
	protected $_value;
	protected $_operator = ' = ';

	function __construct($field, $value, $options = array()) {
		$this->_field = $field;
		$this->_value = $value;
		$this->_options = $options;

		$filter = new Zend_Filter_Word_DashToCamelCase();
		foreach ($options as $option => $val) {
			$method = 'set' . $filter->filter($option);
			if (method_exists($this, $method)) {
				$this->$method($val);
			}
		}
	}

	function setOperator($operator) {
		$this->_operator = $operator;
		return $this;
	}

	function setField($field) {
		$this->_field = $field;
		return $this;
	}

	function setValue($value) {
		$this->_value = $value;
		return $this;
	}

	function getOperator() {
		return $this->_operator;
	}

	function getField() {
		return $this->_field;
	}
	function getValue() {
		return $this->_value;
	}
}