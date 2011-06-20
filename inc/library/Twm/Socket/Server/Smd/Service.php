<?php

class Twm_Socket_Server_Smd_Service
{
    /**#@+
     * Service metadata
     * @var string
     */
    protected $_name;
    protected $_return;
    /**#@-*/

    /**
     * Regex for names
     * @var string
     */
    protected $_nameRegex = '/^[a-z][a-z0-9._]+$/i';

    /**
     * Parameter option types
     * @var array
     */
    protected $_paramOptionTypes = array(
        'name'        => 'is_string',
        'optional'    => 'is_bool',
        'default'     => null,
        'description' => 'is_string',
    );

    /**
     * Service params
     * @var array
     */
    protected $_params = array();

    /**
     * Mapping of parameter types to JSON-RPC types
     * @var array
     */
    protected $_paramMap = array(
        'any'     => 'any',
        'arr'     => 'array',
        'array'   => 'array',
        'assoc'   => 'object',
        'bool'    => 'boolean',
        'boolean' => 'boolean',
        'dbl'     => 'float',
        'double'  => 'float',
        'false'   => 'boolean',
        'float'   => 'float',
        'hash'    => 'object',
        'integer' => 'integer',
        'int'     => 'integer',
        'mixed'   => 'any',
        'nil'     => 'null',
        'null'    => 'null',
        'object'  => 'object',
        'string'  => 'string',
        'str'     => 'string',
        'struct'  => 'object',
        'true'    => 'boolean',
        'void'    => 'null',
    );

    /**
     * Constructor
     *
     * @param  string|array $spec
     * @return void
     * @throws Twm_Socket_Server_Exception if no name provided
     */
    public function __construct($spec)
    {
        if (is_string($spec)) {
            $this->setName($spec);
        } elseif (is_array($spec)) {
            $this->setOptions($spec);
        }

        if (null == $this->getName()) {
            throw new Twm_Socket_Server_Exception('SMD service description requires a name; none provided');
        }
    }

    /**
     * Set object state
     *
     * @param  array $options
     * @return Twm_Socket_Server_Smd_Service
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            if ('options' == strtolower($key)) {
                continue;
            }
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set service name
     *
     * @param  string $name
     * @return Twm_Socket_Server_Smd_Service
     * @throws Twm_Socket_Server_Exception
     */
    public function setName($name)
    {
        $name = (string) $name;
        if (!preg_match($this->_nameRegex, $name)) {
            throw new Twm_Socket_Server_Exception(sprintf('Invalid name "%s" provided for service; must follow PHP method naming conventions', $name));
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Add a parameter to the service
     *
     * @param  string|array $type
     * @param  array $options
     * @param  int|null $order
     * @return Twm_Socket_Server_Smd_Service
     */
    public function addParam($type, array $options = array(), $order = null)
    {
        if (is_string($type)) {
            $type = $this->_validateParamType($type);
        } elseif (is_array($type)) {
            foreach ($type as $key => $paramType) {
                $type[$key] = $this->_validateParamType($paramType);
            }
        } else {
            throw new Twm_Socket_Server_Exception('Invalid param type provided');
        }

        $paramOptions = array(
            'type' => $type,
        );
        foreach ($options as $key => $value) {
            if (in_array($key, array_keys($this->_paramOptionTypes))) {
                if (null !== ($callback = $this->_paramOptionTypes[$key])) {
                    if (!$callback($value)) {
                        continue;
                    }
                }
                $paramOptions[$key] = $value;
            }
        }

        $this->_params[] = array(
            'param' => $paramOptions,
            'order' => $order,
        );

        return $this;
    }

    /**
     * Add params
     *
     * Each param should be an array, and should include the key 'type'.
     *
     * @param  array $params
     * @return Twm_Socket_Server_Smd_Service
     */
    public function addParams(array $params)
    {
        ksort($params);
        foreach ($params as $options) {
            if (!is_array($options)) {
                continue;
            }
            if (!array_key_exists('type', $options)) {
                continue;
            }
            $type  = $options['type'];
            $order = (array_key_exists('order', $options)) ? $options['order'] : null;
            $this->addParam($type, $options, $order);
        }
        return $this;
    }

    /**
     * Overwrite all parameters
     *
     * @param  array $params
     * @return Twm_Socket_Server_Smd_Service
     */
    public function setParams(array $params)
    {
        $this->_params = array();
        return $this->addParams($params);
    }

    /**
     * Get all parameters
     *
     * Returns all params in specified order.
     *
     * @return array
     */
    public function getParams()
    {
        $params = array();
        $index  = 0;
        foreach ($this->_params as $param) {
            if (null === $param['order']) {
                if (array_search($index, array_keys($params), true)) {
                    ++$index;
                }
                $params[$index] = $param['param'];
                ++$index;
            } else {
                $params[$param['order']] = $param['param'];
            }
        }
        ksort($params);
        return $params;
    }

    /**
     * Set return type
     *
     * @param  string|array $type
     * @return Twm_Socket_Server_Smd_Service
     */
    public function setReturn($type)
    {
        if (is_string($type)) {
            $type = $this->_validateParamType($type, true);
        } elseif (is_array($type)) {
            foreach ($type as $key => $returnType) {
                $type[$key] = $this->_validateParamType($returnType, true);
            }
        } else {
            throw new Twm_Socket_Server_Exception('Invalid param type provided ("' . gettype($type) .'")');
        }
        $this->_return = $type;
        return $this;
    }

    /**
     * Get return type
     *
     * @return string|array
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Cast service description to array
     *
     * @return array
     */
    public function toArray()
    {
        $name       = $this->getName();
        $parameters = $this->getParams();
        $returns    = $this->getReturn();

        return $paramInfo = compact('parameters', 'returns');
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return print_r($this->toArray(), true);
    }

    /**
     * Validate parameter type
     *
     * @param  string $type
     * @return true
     * @throws Twm_Socket_Server_Exception
     */
    protected function _validateParamType($type, $isReturn = false)
    {
        if (!is_string($type)) {
            throw new Twm_Socket_Server_Exception('Invalid param type provided ("' . $type .'")');
        }

        if (!array_key_exists($type, $this->_paramMap)) {
            $type = 'object';
        }

        $paramType = $this->_paramMap[$type];
        if (!$isReturn && ('null' == $paramType)) {
            throw new Twm_Socket_Server_Exception('Invalid param type provided ("' . $type . '")');
        }

        return $paramType;
    }
}

