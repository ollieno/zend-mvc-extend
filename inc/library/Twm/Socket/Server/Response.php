<?php

class Twm_Socket_Server_Response
{
    /**
     * Response error
     * @var null|Twm_Socket_Server_Error
     */
    protected $_error;

    /**
     * Result
     * @var mixed
     */
    protected $_result;

    /**
     * Service map
     * @var Twm_Socket_Server_Smd
     */
    protected $_serviceMap;

    /**
     * Set result
     *
     * @param  mixed $value
     * @return Twm_Socket_Server_Response
     */
    public function setResult($value)
    {
        $this->_result = $value;
        return $this;
    }

    /**
     * Get result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    // RPC error, if response results in fault
    /**
     * Set result error
     *
     * @param  Twm_Socket_Server_Error $error
     * @return Twm_Socket_Server_Response
     */
    public function setError(Twm_Socket_Server_Error $error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * Get response error
     *
     * @return null|Twm_Socket_Server_Error
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Is the response an error?
     *
     * @return bool
     */
    public function isError()
    {
        return $this->getError() instanceof Twm_Socket_Server_Error;
    }

    /**
     * Retrieve args
     *
     * @return mixed
     */
    public function getArgs()
    {
        return $this->_args;
    }

    /**
     * Set args
     *
     * @param mixed $args
     * @return self
     */
    public function setArgs($args)
    {
        $this->_args = $args;
        return $this;
    }

    /**
     * Set service map object
     *
     * @param  Twm_Socket_Server_Smd $serviceMap
     * @return Twm_Socket_Server_Response
     */
    public function setServiceMap($serviceMap)
    {
        $this->_serviceMap = $serviceMap;
        return $this;
    }

    /**
     * Retrieve service map
     *
     * @return Twm_Socket_Server_Smd|null
     */
    public function getServiceMap()
    {
        return $this->_serviceMap;
    }

    /**
     * Cast to string (JSON)
     *
     * @return string
     */
    public function __toString()
    {
		if ($this->isError()) {
            $response = array(
                'result' => null,
                'error'  => $this->getError()->toArray(),
            );
        } else {
            $response = array(
                'result' => $this->getResult(),
                'error'  => null,
            );
        }

        return Zend_Json::encode($response) . "\n";
    }
}

