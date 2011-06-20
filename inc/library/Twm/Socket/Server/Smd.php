<?php

class Twm_Socket_Server_Smd
{
    const SMD_VERSION   = '2.0';

    /**
     * Content type regex
     * @var string
     */
    protected $_contentTypeRegex = '#[a-z]+/[a-z][a-z-]+#i';

    /**
     * Service description
     * @var string
     */
    protected $_description;

    /**
     * Services offerred
     * @var array
     */
    protected $_services = array();

    /**
     * Set object state via options
     *
     * @param  array $options
     * @return Twm_Socket_Server_Smd
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set service description
     *
     * @param  string $description
     * @return Twm_Socket_Server_Smd
     */
    public function setDescription($description)
    {
        $this->_description = (string) $description;
        return $this->_description;
    }

    /**
     * Get service description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Add Service
     *
     * @param Twm_Socket_Server_Smd_Service|array $service
     * @return void
     */
    public function addService($service)
    {
        if ($service instanceof Twm_Socket_Server_Smd_Service) {
            $name = $service->getName();
        } elseif (is_array($service)) {
            $service = new Twm_Socket_Server_Smd_Service($service);
            $name = $service->getName();
        } else {
            throw new Twm_Socket_Server_Exception('Invalid service passed to addService()');
        }

        if (array_key_exists($name, $this->_services)) {
            throw new Twm_Socket_Server_Exception('Attempt to register a service already registered detected');
        }
        $this->_services[$name] = $service;
        return $this;
    }

    /**
     * Add many services
     *
     * @param  array $services
     * @return Twm_Socket_Server_Smd
     */
    public function addServices(array $services)
    {
        foreach ($services as $service) {
            $this->addService($service);
        }
        return $this;
    }

    /**
     * Overwrite existing services with new ones
     *
     * @param  array $services
     * @return Twm_Socket_Server_Smd
     */
    public function setServices(array $services)
    {
        $this->_services = array();
        return $this->addServices($services);
    }

    /**
     * Get service object
     *
     * @param  string $name
     * @return false|Twm_Socket_Server_Smd_Service
     */
    public function getService($name)
    {
        if (array_key_exists($name, $this->_services)) {
            return $this->_services[$name];
        }
        return false;
    }

    /**
     * Return services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->_services;
    }

    /**
     * Remove service
     *
     * @param  string $name
     * @return boolean
     */
    public function removeService($name)
    {
        if (array_key_exists($name, $this->_services)) {
            unset($this->_services[$name]);
            return true;
        }
        return false;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        $SMDVersion  = self::SMD_VERSION;
        $service = compact('SMDVersion');

        $services = $this->getServices();
        if (!empty($services)) {
            $service['services'] = array();
            foreach ($services as $name => $svc) {
                $service['services'][$name] = $svc->toArray();
            }
            $service['methods'] = $service['services'];
        }

        return $service;
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
}

