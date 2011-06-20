<?php

class Twm_Socket_Server extends Zend_Server_Abstract {

	/**
	 * Request object
	 * @var Twm_Socket_Server_Request
	 */
	protected $_request;

	protected $_instances = array();


	protected $_connected = false;
	/**
	 * Response object
	 * @var Twm_Socket_Server_Response
	 */
	protected $_response;

    /**
     * SMD object
     * @var Twm_Socket_Server_Smd
     */
    protected $_serviceMap;

    /**
     * SMD class accessors
     * @var array
     */
    protected $_smdMethods;

	private $_socket;
	protected $_address = '0.0.0.0';
	protected $_remote_addres;
	protected $_remote_port;
	protected $_port = '443';
	protected $_domain = AF_INET;
	protected $_type = SOCK_STREAM;
	protected $_protocol = SOL_TCP;
	private $_clients = array();

	public function __construct($options=array()) {
		parent::__construct();

		$this->setOptions($options);
	}

	public function setOptions($options) {
		if (!is_array($options)) {
			throw new Exception('options should be an array');
		}

		foreach ($options as $option => $value) {
			$method_name = 'set' . ucfirst($option);
			if (method_exists($this, $method_name)) {
				$this->{$method_name}($value);
			}
		}
	}

	public function setAddress($address) {
		$this->_address = $address;
		return $this;
	}

	public function setPort($port) {
		$this->_port = $port;
		return $this;
	}

	public function setDomain($domain) {
		$this->_domain = $domain;
		return $this;
	}

	public function setProtocol($protocol) {
		$this->_protocol = $protocol;
		return $this;
	}

	public function setType($type) {
		$this->_type = $type;
		return $this;
	}

	/**
	 * Set request object
	 *
	 * @param  Twm_Socket_Server_Request $request
	 * @return Twm_Socket_Server
	 */
	public function setRequest(Twm_Socket_Server_Request $request) {
		$this->_request = $request;
		return $this;
	}

	/**
	 * Get JSON-RPC request object
	 *
	 * @return Twm_Socket_Server_Request
	 */
	public function getRequest() {
		if (null === ($request = $this->_request)) {
			$this->setRequest(new Twm_Socket_Server_Request());
		}
		return $this->_request;
	}

	/**
	 * Set response object
	 *
	 * @param  Twm_Socket_Server_Response $response
	 * @return Twm_Socket_Server
	 */
	public function setResponse(Twm_Socket_Server_Response $response) {
		$this->_response = $response;
		return $this;
	}

	/**
	 * Get response object
	 *
	 * @return Twm_Socket_Server_Response
	 */
	public function getResponse() {
		if (null === ($response = $this->_response)) {
			$this->setResponse(new Twm_Socket_Server_Response());
		}
		return $this->_response;
	}

	protected function _getConnections() {
		$return = array();
		if (is_resource($this->_socket)) {
			$return[] = $this->_socket;
		}
		foreach ($this->_clients as $client) {
			if (is_resource($this->_socket)) {
				$return[] = $client;
			}
		}
		return $return;
	}

	/**
	 * Attach a function or callback to the server
	 *
	 * @param  string|array $function Valid PHP callback
	 * @param  string $namespace  Ignored
	 * @return Twm_Socket_Server
	 */
	public function addFunction($function, $namespace = '') {
		if (!is_string($function) && (!is_array($function) || (2 > count($function)))) {
			throw new Twm_Socket_Server_Exception('Unable to attach function; invalid');
		}

		if (!is_callable($function)) {
			throw new Twm_Socket_Server_Exception('Unable to attach function; does not exist');
		}

		$argv = null;
		if (2 < func_num_args()) {
			$argv = func_get_args();
			$argv = array_slice($argv, 2);
		}

		require_once 'Zend/Server/Reflection.php';
		if (is_string($function)) {
			$method = Zend_Server_Reflection::reflectFunction($function, $argv, $namespace);
		} else {
			$class = array_shift($function);
			$action = array_shift($function);
			$reflection = Zend_Server_Reflection::reflectClass($class, $argv, $namespace);
			$methods = $reflection->getMethods();
			$found = false;
			foreach ($methods as $method) {
				if ($action == $method->getName()) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$this->fault("Method not found '$action'", -32601);
				return $this;
			}
		}

		$definition = $this->_buildSignature($method);
		$this->_addMethodServiceMap($definition);

		return $this;
	}

	/**
	 * Add service method to service map
	 *
	 * @param  Zend_Server_Reflection_Function $method
	 * @return void
	 */
	protected function _addMethodServiceMap(Zend_Server_Method_Definition $method) {
		$serviceInfo = array(
			'name' => $method->getName(),
			'return' => $this->_getReturnType($method),
		);
		$params = $this->_getParams($method);
		$serviceInfo['params'] = $params;
		$serviceMap = $this->getServiceMap();
		if (false !== $serviceMap->getService($serviceInfo['name'])) {
			$serviceMap->removeService($serviceInfo['name']);
		}
		$serviceMap->addService($serviceInfo);
	}

	/**
     * Retrieve SMD object
     *
     * @return Twm_Socket_Server_Smd
     */
    public function getServiceMap()
    {
        if (null === $this->_serviceMap) {
            $this->_serviceMap = new Twm_Socket_Server_Smd();
        }
        return $this->_serviceMap;
    }

	/**
     * Get method return type
     *
     * @param  Zend_Server_Reflection_Function_Abstract $method
     * @return string|array
     */
    protected function _getReturnType(Zend_Server_Method_Definition $method)
    {
        $return = array();
        foreach ($method->getPrototypes() as $prototype) {
            $return[] = $prototype->getReturnType();
        }
        if (1 == count($return)) {
            return $return[0];
        }
        return $return;
    }

	/**
     * Get method param type
     *
     * @param  Zend_Server_Reflection_Function_Abstract $method
     * @return string|array
     */
    protected function _getParams(Zend_Server_Method_Definition $method)
    {
        $params = array();
        foreach ($method->getPrototypes() as $prototype) {
            foreach ($prototype->getParameterObjects() as $key => $parameter) {
                if (!isset($params[$key])) {
                    $params[$key] = array(
                        'type'     => $parameter->getType(),
                        'name'     => $parameter->getName(),
                        'optional' => $parameter->isOptional(),
                    );
                    if (null !== ($default = $parameter->getDefaultValue())) {
                        $params[$key]['default'] = $default;
                    }
                    $description = $parameter->getDescription();
                    if (!empty($description)) {
                        $params[$key]['description'] = $description;
                    }
                    continue;
                }
                $newType = $parameter->getType();
                if (!is_array($params[$key]['type'])) {
                    if ($params[$key]['type'] == $newType) {
                        continue;
                    }
                    $params[$key]['type'] = (array) $params[$key]['type'];
                } elseif (in_array($newType, $params[$key]['type'])) {
                    continue;
                }
                array_push($params[$key]['type'], $parameter->getType());
            }
        }
        return $params;
    }

	/**
	 * Register a class with the server
	 *
	 * @param  string $class
	 * @param  string $namespace Ignored
	 * @param  mixed $argv Ignored
	 * @return Twm_Socket_Server
	 */
	public function setClass($class, $namespace = '', $argv = null) {
		$argv = null;
		if (3 < func_num_args()) {
			$argv = func_get_args();
			$argv = array_slice($argv, 3);
		}

		require_once 'Zend/Server/Reflection.php';
		$reflection = Zend_Server_Reflection::reflectClass($class, $argv, $namespace);

		foreach ($reflection->getMethods() as $method) {
			$definition = $this->_buildSignature($method, $class);
			$this->_addMethodServiceMap($definition);
		}
		return $this;
	}

	/**
	 * Indicate fault response
	 *
	 * @param  string $fault
	 * @param  int $code
	 * @return false
	 */
	public function fault($fault = null, $code = 404, $data = null) {
		require_once 'Zend/Json/Server/Error.php';
		$error = new Twm_Socket_Server_Error($fault, $code, $data);
		$this->getResponse()->setError($error);
		return $error;
	}

	/**
	 * Load function definitions
	 *
	 * @param  array|Zend_Server_Definition $definition
	 * @return void
	 */
	public function loadFunctions($definition) {
		if (!is_array($definition) && (!$definition instanceof Zend_Server_Definition)) {
			throw new Twm_Socket_Server_Exception('Invalid definition provided to loadFunctions()');
		}

		foreach ($definition as $key => $method) {
			$this->_table->addMethod($method, $key);
			$this->_addMethodServiceMap($method);
		}
	}

	public function setPersistence($mode) {

	}

	/**
	 * Translate PHP type to type
	 *
	 * @param  string $type
	 * @return string
	 */
	protected function _fixType($type) {
		return $type;
	}

	public function handle($request=false) {
		if (!$this->_connected) {
			$this->connect();
		}

		$read = $this->_getConnections();
		$write = $this->_getConnections();
		$exception = $this->_getConnections();

		while (($events = socket_select($read, $write, $exception, 2)) !== false) {
			if ($events > 0) {
				foreach ($read as $socket) {
					if (!isset($this->_clients[(int) $socket])) {
						$client = socket_accept($socket);
						$this->_clients[(int) $client] = $client;
					} else {
						$input = socket_read($socket, 2048);

						$request = new Twm_Socket_Server_Request();
						$this->setRequest($request->setMethod(trim($input)));

						$result = $this->_handle();

						$response = new Twm_Socket_Server_Response();
						$this->setResponse($response->setResult($result));

						if (is_resource($socket)) {
							@socket_write($socket, $response->__toString());
						}
					}
				}
				
				if (($result = $this->_handleLoop()) !== FALSE) {
					if (!empty($result)) {
						$response = new Twm_Socket_Server_Response();
						$this->setResponse($response->setResult($result));

						foreach ($write as $socket) {
							socket_write($socket, $response->__toString());
						}
					}
				}
				foreach ($exception as $socket) {
					if (isset($this->_clients[(int) $socket])) {
						unset($this->_clients[(int) $socket]);
					}
				}
			}
			$read = $this->_getConnections();
			$write = $this->_getConnections();
			$exception = $this->_getConnections();
		}
		$this->disconnect();
	}

	protected function _handleLoop() {
		$method = 'loop';
        if (!$this->_table->hasMethod($method)) {
            return false; // loop method is optional
        }

		$params		   = array();
        $invocable     = $this->_table->getMethod($method);
        $serviceMap    = $this->getServiceMap();
        $service       = $serviceMap->getService($method);
        $serviceParams = $service->getParams();

        if (count($params) < count($serviceParams)) {
            $params = $this->_getDefaultParams($params, $serviceParams);
        }

        try {
            $result = $this->_dispatch($invocable, $params);
        } catch (Exception $e) {
            return $this->fault($e->getMessage(), $e->getCode(), $e);
        }

		return $result;
	}


	/**
     * Internal method for handling request
     *
     * @return void
     */
    protected function _handle()
    {
        $request = $this->getRequest();

        $method = $request->getMethod();
        if (!$this->_table->hasMethod($method)) {
            return $this->fault("Method not found '$method'", -1001);
        }

        $params        = $request->getParams();
        $invocable     = $this->_table->getMethod($method);
        $serviceMap    = $this->getServiceMap();
        $service       = $serviceMap->getService($method);
        $serviceParams = $service->getParams();

        if (count($params) < count($serviceParams)) {
            $params = $this->_getDefaultParams($params, $serviceParams);
        }

        try {
            $result = $this->_dispatch($invocable, $params);
        } catch (Exception $e) {
            return $this->fault($e->getMessage(), $e->getCode(), $e);
        }

		return $result;
    }
	
	public function getError() {
		$error = socket_strerror(socket_last_error($this->_socket));
		socket_clear_error($this->_socket);
		return $error;
	}

	/**
     * Dispatch method
     *
     * @param  Zend_Server_Method_Definition $invocable
     * @param  array $params
     * @return mixed
     */
    protected function _dispatch(Zend_Server_Method_Definition $invocable, array $params)
    {
        $callback = $invocable->getCallback();
        $type     = $callback->getType();

        if ('function' == $type) {
            $function = $callback->getFunction();
            return call_user_func_array($function, $params);
        }

        $class  = $callback->getClass();
        $method = $callback->getMethod();

        if ('static' == $type) {
            return call_user_func_array(array($class, $method), $params);
        }

		if (!isset($this->_instances[$invocable->getName()])) {
			$object = $invocable->getObject();

			if (!is_object($object)) {
				$invokeArgs = $invocable->getInvokeArguments();
				if (!empty($invokeArgs)) {
					$reflection = new ReflectionClass($class);
					$object     = $reflection->newInstanceArgs($invokeArgs);
				} else {
					$object = new $class;
				}
			}
			$this->_instances[$invocable->getName()] = $object;
		}
		$object = $this->_instances[$invocable->getName()];

        return call_user_func_array(array($object, $method), $params);
    }

	public function connect() {
		if (!$this->_connected) {
			$this->_socket = socket_create($this->_domain, $this->_type, $this->_protocol);

			if (!@socket_set_option($this->_socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
				throw new Twm_Socket_Server_Exception("Could not set SO_REUSEADDR: " . $this->getError());
			}
			if (!@socket_bind($this->_socket, $this->_address, $this->_port)) {
				throw new Twm_Socket_Server_Exception("Could not bind socket to [$this->_address - $this->_port]: " . $this->getError());
			}
			if (!@socket_getsockname($this->_socket, $this->_remote_addres, $this->_remote_port)) {
				throw new Twm_Socket_Server_Exception("Could not retrieve local address & port: " . $this->getError());
			}
			if (!@socket_set_nonblock($this->_socket)) {
				throw new Twm_Socket_Server_Exception("Could not set socket non_block: " . $this->getError());
			}
			$this->_connected = true;

			$this->listen();
		}
		return $this;
	}

	public function listen($backlog = 128) {
		if (!is_resource($this->_socket)) {
			throw new Twm_Socket_Server_Exception("Invalid socket or resource");
		} elseif (!@socket_listen($this->_socket, $backlog)) {
			throw new Twm_Socket_Server_Exception("Could not listen to {$this->_address} - {$this->_port}: " . $this->get_error());
		}
	}

	public function disconnect() {
		if ($this->_connected) {
			@socket_close($this->_socket);
		}
	}

	public function __destruct() {
		if (is_resource($this->_socket)) {
			$this->disconnect();
		}
	}

}