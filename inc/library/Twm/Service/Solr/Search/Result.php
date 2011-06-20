<?php

class Twm_Service_Solr_Search_Result implements ArrayAccess, Iterator {

	protected $_result;
	protected $_query;
	protected $_postion = 0;

	public function __construct($data) {
		$this->_result = Zend_Json::decode($data);
	}

	public function getResult($section=false) {
		if ($section) {
			return $this->_result[$section];
		}
		return $this->_result;
	}

	// with function isset()
	public function offsetExists($offset) {
		return isset($this->_result['response']['docs'][$offset]);
	}

	public function offsetGet($offset) {
		return $this->_result['response']['docs'][$offset];
	}

	public function offsetSet($offset, $value) {
		return $this->_result['response']['docs'][$offset] = $value;
	}

	// with function unset()
	public function offsetUnset($offset) {
		unset($this->_result['response']['docs'][$offset]);
	}

	public function rewind() {
		$this->_position = 0;
	}

	public function current() {
		$data = $this->_result['response']['docs'][$this->_position];
		$doc = new Twm_Service_Solr_Document();
		foreach ($data as $key => $val) {
			$field = new Twm_Service_Solr_Field($key, $val);
			$doc->addField($field);
		}
		$hit = new Twm_Service_Solr_Search_QueryHit($doc);
		$hit->score = (isset($data['score']))?$data['score']:"";

		return $hit;
	}

	public function key() {
		return $this->_position;
	}

	public function next() {
		++$this->_position;
	}

	public function valid() {
		return isset($this->_result['response']['docs'][$this->_position]);
	}

	public function getFacetmap() {
		return new Twm_Service_Solr_Search_Facetmap($this);
	}

	public function getNumFound() {
		return $this->_result['response']['numFound'];
	}

	public function getLimit() {
		return $this->_result['responseHeader']['params']['rows'];
	}

	public function getStart() {
		return $this->_result['response']['start'];
	}

	public function getQueryParams() {
		return $this->_result['responseHeader']['params'];
	}

	/**
	 *
	 * @return Twm_Service_Solr_Search_Query
	 */
	public function getQuery() {
		if (null === $this->_query) {
			$query = new Twm_Service_Solr_Search_Query();
			$query->setParams($this->getQueryParams());
			$this->_query = $query;
		}
		return $this->_query;
	}

}