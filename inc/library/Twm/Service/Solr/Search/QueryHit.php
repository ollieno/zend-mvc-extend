<?php

class Twm_Service_Solr_Search_QueryHit {

	/**
     * Object handle of the document associated with this hit
     * @var Twm_Search_Solr_Document
     */
    protected $_document = null;

	public function  __construct($document) {
		$this->_document = $document;
	}

	public function __get($offset) {
		return $this->getDocument()->getFieldValue($offset);
	}

	public function getDocument()
    {
        return $this->_document;
    }

}
