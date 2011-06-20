<?php

class Twm_Core_Design_Layout_Adapter_File extends Twm_Core_Design_Layout_Adapter_Abstract {

    protected $_layoutdirectory = "layouts";

    function __construct($options) {
        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'layoutdirectoryname':
                    $this->_layoutdirectory = $value;
                    break;
                default:
                    break;
            }
        }
    }

    public function getLayoutConfig($section="") {
        $Package = Twm::getDesign()->getPackage();
        $paths = $Package->getBasePaths();

        $config = array();
        $loaded = array();
        $ds = DIRECTORY_SEPARATOR;

        foreach ($paths as $path) {
            $path = $path . $ds . $this->_layoutdirectory . $ds;
            $Dir = new DirectoryIterator($path);
            foreach ($Dir as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $filename = $fileInfo->getFilename();
                    $module = explode('.', $filename);
                    array_pop($module);
                    $module = implode('.', $module);

                    if (!isset($loaded[$module])) {
                        $config[$module] = $this->_loadLayoutConfig($path . $ds . $filename);
                    }
                }
            }
        }
        $this->_mergeConfigs($config);
        $mergedConfig = array();
        foreach ($config as $module => $config) {
            $mergedConfig = array_merge_recursive($mergedConfig, $config);
        }
        $sectionConfig = null;
        if (isset($mergedConfig[$section])) {
            $mergedConfig = array_merge_recursive($mergedConfig['default'], $mergedConfig[$section]);
        }

        return $mergedConfig;
    }

    protected function _mergeConfigs(&$data) {
        foreach ($data as $key => $value) {
            if (($key === 'block' || $key === 'reference' || $key === 'action') && !isset($value[0])) {
                if (!isset($value[0])) {
                    $data[$key] = array($data[$key]);
                }
            }
            if (is_array($data[$key])) {
                $this->_mergeConfigs($data[$key]);
            }
        }
    }

    /**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function mergeOptionsReplace(array $array1, $array2 = null) {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key])) ? $this->mergeOptionsReplace($array1[$key], $array2[$key]) : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    public function mergeOptions(array $array1, $array2 = null) {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key])) ? $this->mergeOptions($array1[$key], $array2[$key]) : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    protected function _loadLayoutConfig($file) {
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($suffix) {
            case 'ini':
                $config = new Zend_Config_Ini($file);
                break;

            case 'xml':
                $config = new Zend_Config_Xml($file);
                break;

            case 'json':
                $config = new Zend_Config_Json($file);
                break;

            case 'yaml':
                $config = new Zend_Config_Yaml($file);
                break;

            case 'php':
            case 'inc':
                $config = include $file;
                if (!is_array($config)) {
                    throw new Zend_Application_Exception('Invalid layout file provided; PHP file does not return array value');
                }
                return $config;
                break;
            default:
                throw new Zend_Application_Exception('Invalid layout file provided; unknown config type');
        }

        return $config->toArray();
    }

}