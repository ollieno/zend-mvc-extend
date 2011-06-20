<?php

class Twm_Socket_Server_Error {

	/**
     * Current code
     * @var int
     */
    protected $_code = -1000;

    /**
     * Error data
     * @var mixed
     */
    protected $_data;

    /**
     * Error message
     * @var string
     */
    protected $_message;

	
	public function __construct($message = null, $code = -1000, $data = null)
    {
		$this->setMessage($message)
             ->setCode($code)
             ->setData($data);
    }

	/**
     * Set error code
     *
     * @param  int $code
     * @return Zend_Json_Server_Error
     */
    public function setCode($code)
    {
        $code = (int) $code;
        $this->_code = $code;

        return $this;
    }

    /**
     * Get error code
     *
     * @return int|null
     */
    public function getCode()
    {
        return $this->_code;
    }

	 /**
     * Set error message
     *
     * @param  string $message
     * @return Zend_Json_Server_Error
     */
    public function setMessage($message)
    {
        if (!is_scalar($message)) {
            return $this;
        }

        $this->_message = (string) $message;
        return $this;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Set error data
     *
     * @param  mixed $data
     * @return Zend_Json_Server_Error
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Get error data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Cast error to array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
            'data'    => $this->getData(),
        );
    }

	/**
     * Cast to string (JSON)
     *
     * @return string
     */
    public function __toString()
    {
        return print_r($this->toArray(), true);
    }
}