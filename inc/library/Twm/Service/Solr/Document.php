<?php

class Twm_Service_Solr_Document {

	/**
	 * Associative array Twm_Search_Solr_Field objects where the keys to the
	 * array are the names of the fields.
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Proxy method for getFieldValue(), provides more convenient access to
	 * the string value of a field.
	 *
	 * @param  $offset
	 * @return string
	 */
	public function __get($offset) {
		return $this->getFieldValue($offset);
	}

	/**
	 * Add a field object to this document.
	 *
	 * @param Twm_Search_Solr_Field $field
	 * @return Twm_Search_Solr_Document
	 */
	public function addField(Twm_Service_Solr_Field $field) {
		$this->_fields[] = $field;

		return $this;
	}

	/**
	 * Return an array with the names of the fields in this document.
	 *
	 * @return array
	 */
	public function getFieldNames() {
		$fieldnames = array();
		foreach ($this->_fields as $field) {
			$fieldnames[] = $field->getName();
		}
		return $fieldnames;
	}

	/**
	 * Returns Twm_Search_Solr_Field object for a named field in this document.
	 *
	 * @param string $fieldName
	 * @return Array of Twm_Search_Solr_Field matching the fieldname
	 */
	public function getField($fieldName) {
		return $this->getFields($fieldName);
	}

	/**
	 * Returns the string value of a named field in this document.
	 *
	 * @see __get()
	 * @return string
	 */
	public function getFieldValue($fieldName) {
		$fields = $this->getField($fieldName);
		if (count($fields) == 1) {
			return $fields[0]->value;
		}
		$values = array();
		foreach ($fields as $field) {
			$values[] = $field->value;
		}
		return $values;
	}

	public function getFields($fieldName=false) {
		$result = array();
		if ($fieldName) {
			foreach ($this->_fields as $field) {
				if ($field->name == $fieldName) {
					$result[] = $field;
				}
			}
			return $result;
		}
		return $this->_fields;
	}

	/**
	 * Returns the string value of a named field in UTF-8 encoding.
	 *
	 * @see __get()
	 * @return string
	 */
	public function getFieldUtf8Value($fieldName) {
		return $this->getField($fieldName)->getUtf8Value();
	}

	public function toArray() {
		$doc = array();

		foreach ($this->getFields() as $field) {
			$doc[$field->name] = $field->value;
		}

		return $doc;
	}

	public function toXml() {
		$xml = new SimpleXMLElement('<doc/>');

		foreach ($this->getFields() as $field) {
			$xml->addChild('field', htmlspecialchars($field->value, ENT_NOQUOTES, 'UTF-8'))
				->addAttribute('name', $field->name);
		}

		return $xml;
	}

	public function toJson() {
		$data = $this->toArray();
		return Zend_Json::encode($data);
	}

}