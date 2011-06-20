<?php

class Twm_Service_Solr_Search_Facetmap_Facet implements IteratorAggregate {

    protected $_name;
    protected $_filters = array();
    protected $_count = 0;
    protected $_selected = null;

    function __construct($name, $data) {
	$this->_name = $name;
	$limit = count($data);
	for ($i = 0; $i < $limit; $i++) {
	    $value = $data[$i];
	    $count = $data[++$i];
	    $filter = new Twm_Service_Solr_Search_Facetmap_Facet_Filter($name, $value, $count);
	    $this->_filters[] = $filter;
	    $this->_count += $filter->getCount();
	}
    }

    public function getName() {
	return $this->_name;
    }

    public function numFilters() {
	return count($this->_filters);
    }

    public function hasFilters() {
	return ($this->numFilters() > 0);
    }

    public function getCount() {
	return $this->_count;
    }

    function isSelected() {
	if (null === $this->_selected) {
	    foreach ($this->_filters as $filter) {
		if ($filter->isSelected()) {
		    $this->_selected = true;
		    return $this->_selected;
		}
	    }
	}
	return $this->_selected;
    }

    function getSelectedFilters() {
	$selected = array();
	foreach ($this->_filters as $filter) {
	    if ($filter->isSelected()) {
		$selected[] = $filter;
	    }
	}
	return $selected;
    }

    function getIterator() {
	return new ArrayIterator($this->_filters);
    }
    
    function getFilters()
    {
	return $this->_filters;
    }
}