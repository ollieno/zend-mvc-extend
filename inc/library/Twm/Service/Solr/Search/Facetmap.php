<?php

class Twm_Service_Solr_Search_Facetmap implements IteratorAggregate {

	/**
	 * @var Twm_Service_Solr_Search_Result
	 */
	public static $result;
	protected $_queries = array();
	protected $_facets = array();
	protected $_facetsIndex = array();
	protected $_facetsPosition = 0;
	protected $_dates = array();

	public function __construct(Twm_Service_Solr_Search_Result $result) {
		self::$result = $result;
		$this->_parse();
	}

	protected function _parse() {

		$facetCounts = self::$result->getResult('facet_counts');
		$this->_parseFacetCounts($facetCounts);
	}

	protected function _parseFacetCounts($facetCounts) {
		if (!isset($facetCounts['facet_fields'])) {
			return;
		}
		foreach ($facetCounts['facet_fields'] as $name => $value) {
			$facet = new Twm_Service_Solr_Search_Facetmap_Facet($name, $value);
			$this->_facets[] = $facet;
			$this->_facetsIndex[$name] = count($this->_facets) - 1;
		}
		foreach ($facetCounts['facet_queries'] as $facet => $value) {
			// todo parse facet queries
		}
		foreach ($facetCounts['facet_dates'] as $facet => $value) {
			// todo parse facet dates
		}
	}

	public function getIterator() {
		return new ArrayIterator($this->_facets);
	}

	public function getFacets() {
		return $this->_facets;
	}

	public function getFacet($name) {
		if (isset($this->_facetsIndex[$name])) {
			return $this->_facets[$this->_facetsIndex[$name]];
		}
		return null;
	}

	function getSelectedFilters() {
		$selected = array();
		foreach ($this->_facets as $facet) {
			$selected = array_merge($selected, $facet->getSelectedFilters());
		}
		return $selected;
	}

    function getCount()
    {
        $count = 0;
        foreach ($this->_facets as $facet)
        {
            $count += $facet->getCount();
        }
        return $count;
    }

}