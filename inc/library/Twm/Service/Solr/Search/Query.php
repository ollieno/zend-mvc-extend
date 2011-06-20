<?php

class Twm_Service_Solr_Search_Query {

    protected $_prefix = "";
    protected $_postfix = "";
    protected $_start = 0;
    protected $_sort = array();
    protected $_limit = 50;
    protected $_isFacet = false;
    protected $_facetMinCount;
    protected $_query = '';
    protected $_writerType = 'json';
    protected $_fields = array();
    protected $_facetFields = array();
    protected $_facetQueries = array();
    protected $_filterQueries = array();
    protected $_rawget = false;

    public function __construct($query = "") {

        if (!empty($query)) {
            $this->setQuery($query);
        }
    }

    public function setQueryPrefix($value) {
        $this->_prefix = $value;
        return $this;
    }

    public function setQueryPostfix($value) {
        $this->_postfix = $value;
        return $this;
    }

    public function getQueryPrefix() {
        return $this->_prefix;
    }

    public function getQueryPostfix() {
        return $this->_postfix;
    }

    public function setStart($start) {
        $this->_start = $start;
        return $this;
    }

    public function addSort($sort) {
        $this->_sort[] = $sort;
    }

    public function getStart() {
        return $this->_start;
    }

    public function setRows($rows) {
        return $this->setLimit($rows);
    }

    public function setLimit($limit) {
        $this->_limit = $limit;
        return $this;
    }

    public function getLimit() {
        return $this->_limit;
    }

    public function setFacet($value) {
        if (!is_bool($value)) {
            throw new Twm_Service_Solr_Exception('Facet value must be a boolean');
        }

        $this->_isFacet = $value;
        return $this;
    }

    public function getFacet() {
        return $this->_isFacet;
    }

    public function setQuery($query) {
        $this->_query = $query;
        return $this;
    }

    public function getQuery() {
        $query = $this->_query;
        if (!empty($this->_prefix)) {
            $query = preg_replace('/^' . preg_quote($this->_prefix) . '/', '', $query);
        }
        if (!empty($this->_postfix)) {
            $query = preg_replace('/' . preg_quote($this->_postfix) . '$/', '', $query);
        }
        return $query;
    }

    public function isFacet($bool = null) {
        if (null === $bool) {
            return $this->_isFacet;
        }
        $this->_isFacet = $bool;
        return $this;
    }

    public function addField($field) {
        $this->isFacet(true);
        if (is_array($field)) {
            foreach ($field as $f) {
                $this->addField($f);
            }
        } else if (is_string($field)) {
            $fields = explode(',', $field);
            foreach ($fields as $field) {
                $field = trim($field);
                $field = new Twm_Service_Solr_Search_Field($field);
                $this->addField($field);
            }
        } else if ($field instanceof Twm_Service_Solr_Search_Field) {
            $this->_fields[] = $field;
        } else {
            throw new Twm_Service_Solr_Exception('Trying to add a unknown field');
        }
        return $this;
    }

    public function addFacetField($field) {
        $this->isFacet(true);
        if (is_array($field)) {
            foreach ($field as $f) {
                $this->addFacetField($f);
            }
        } else if (is_string($field)) {
            $fields = explode(',', $field);
            foreach ($fields as $field) {
                $field = new Twm_Service_Solr_Search_Facetmap_Field($field);
                $this->_facetFields[] = $field;
            }
        } else if ($field instanceof Twm_Service_Solr_Search_Facetmap_Field) {
            $this->_facetFields[] = $field;
        } else {
            throw new Twm_Service_Solr_Exception('Trying to add a unknown facetfield');
        }
        return $this;
    }

    public function setFacetMinCount($count) {
        $this->_facetMinCount = $count;
        return $this;
    }

    public function getFacetFields() {
        return $this->_facetFields;
    }

    public function getFields() {
        return $this->_fields;
    }

    public function addFacetQuery($query) {
        $this->_facetQueries[] = $query;
        return $this;
    }

    public function getFacetQueries() {
        return $this->_facetQueries;
    }

    public function addFilterQuery($filter) {
        if (is_array($filter)) {
            foreach ($filter as $f) {
                $this->addFilterQuery($f);
            }
        } else if (is_string($filter)) {
            $this->_filterQueries[] = $filter;
        } else {
            throw new Twm_Service_Solr_Exception('Trying to add a unknown facetfield');
        }
        return $this;
    }

    public function removeFilterQuery(Twm_Service_Solr_Search_Facetmap_Facet_Filter $filter) {
        $count = count($this->_filterQueries);
        for ($i = 0; $i < $count; $i++) {
            if ($this->_filterQueries[$i] == $filter->getQuery()) {
                unset($this->_filterQueries[$i]);
                break;
            }
        }
    }

    public function getFilterQueries() {
        return $this->_filterQueries;
    }

    public function setWriterType($type) {
        $this->_writerType = $type;
        return $this;
    }

    public function getWriterType() {
        return $this->_writerType;
    }

    public function setParams($params) {
        foreach ($params as $key => $val) {
            switch ($key) {
                case 'q':
                    $this->setQuery($val);
                    break;
                case 'facet';
                    $this->isFacet($val);
                    break;
                case 'fl':
                    $this->addField($val);
                    break;
                case 'facet.field':
                    $this->addFacetField($val);
                    break;
                case 'start':
                    $this->setStart($val);
                    break;
                case 'wt':
                    $this->setWriterType($val);
                    break;
                case 'rows':
                    $this->setRows($val);
                    break;
                case 'fq':
                    $this->addFilterQuery($val);
                    break;
                case 'facet.mincount':
                    $this->setFacetMinCount($val);
                    break;
                case 'sort':
                    $this->addSort($val);
                    break;
                default:
                    d("no action found for param $key");
                    break;
            }
        }
    }

    public function toArray() {
        $params = array();
        $params['q'] = $this->getQuery();
        if ($this->getFacet()) {

            $facetfields = array();
            $params['facet'] = 'true';
            foreach ($this->_facetFields as $facetfield) {
                $params['facet.field'][] = $facetfield->getName();
            }
            $params['facet.query'] = $this->getFacetQueries();
            if ($this->_facetMinCount) {
                $params['facet.mincount'] = $this->_facetMinCount;
            }
        }
        $params['fq'] = $this->getFilterQueries();
        $fieldnames = array();
        foreach ($this->getFields() as $field) {
            $fieldnames[] = $field->getName();
        }
        $params['fl'] = implode(',', $fieldnames);
        $params['start'] = $this->getStart();
        $params['rows'] = $this->getLimit();
        $params['wt'] = $this->getWriterType();
        if (count($this->_sort) > 0) {
            $params['sort'] = implode(',', $this->_sort);
        }

        return $params;
    }

    public function toUriParams() {
        if ($this->_rawget) {
            return $this->_rawget;
        }
        $params = $this->toArray();
        $uri = "";
        $glue = '';
        foreach ($params as $name => $value) {
            if ($name == 'q') {
                $value = $this->_prefix . $value . $this->_postfix;
            }
            if (is_array($value)) {
                foreach ($value as $val) {
                    $uri .= "{$glue}{$name}=" . urlencode($val);
                }
            } else {
                $uri .= "{$glue}{$name}=" . urlencode($value);
            }
            $glue = '&';
        }
        return $uri;
    }

    public function setRawGet($rawget) {
        $this->_rawget = $rawget;
    }

}