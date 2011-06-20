<?php

class Twm_Paginator_Adapter_Solr implements Zend_Paginator_Adapter_Interface {

	protected $_query;

	public function __construct(Zend_Service_Solr_Search_Query $query) {
		$this->_query = $query;
	}

	public function setRowCount($rowCount) {
		if ($rowCount instanceof Zend_Serivce_Solr_Search_Query) {
			// todo fetch rowcount
		} else if (is_integer($rowCount)) {
			$this->_rowCount = $rowCount;
		} else {
			/**
			 * @see Zend_Paginator_Exception
			 */
			require_once 'Zend/Paginator/Exception.php';

			throw new Zend_Paginator_Exception('Invalid row count');
		}

		return $this;
	}

	/**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_query
			->setStart($offset)
			->setRows($itemCountPerPage);

        return; // todo fetch results
    }


	/**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $this->setRowCount(
                // todo get count
            );
        }

        return $this->_rowCount;
    }
}