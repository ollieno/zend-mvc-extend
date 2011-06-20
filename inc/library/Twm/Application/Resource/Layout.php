<?php

class Twm_Application_Resource_Layout extends Zend_Application_Resource_ResourceAbstract {

	protected $_adapter = null;
	protected $_params = array();
	protected $_layoutAdapter;

	function init() {
		$Layout = Twm::getLayout();
		$Layout->setAdapter($this->getLayoutAdapter());
	}

	public function getLayoutAdapter() {
		if ((null === $this->_layoutAdapter) && (null !== ($adapter = $this->getAdapter()))) {
			$adapter = strtolower($adapter);
			switch ($adapter) {
				case 'file':
					$layoutAdapter = new Twm_Core_Design_Layout_Adapter_File($this->getParams());
					break;
				case 'nosql':
					// TODO: implement nosql adapter
					break;
				case 'db':
					// TODO: implement db adapter
					break;
				default:
					throw new Zend_Application_Exception('Invalid layout adapter provided');
			}
			$this->_layoutAdapter = $layoutAdapter;
		}
		return $this->_layoutAdapter;
	}

	/**
	 * Adapter type to use
	 *
	 * @return string
	 */
	public function getAdapter() {
		return $this->_adapter;
	}

	/**
	 * Set the adapter
	 *
	 * @param  $adapter string
	 * @return Twm_Application_Resource_Layout
	 */
	public function setAdapter($adapter) {
		$this->_adapter = $adapter;
		return $this;
	}

    /**
     * Set the adapter params
     *
     * @param  $adapter string
     * @return Twm_Application_Resource_Layout
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Adapter parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

}