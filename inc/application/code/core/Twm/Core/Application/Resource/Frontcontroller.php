<?php

class Twm_Core_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller {

    /**
     * Initialize Front Controller
     *
     * @return Zend_Controller_Front
     */
    public function init() {

	$front = $this->getFrontController();

	foreach ($this->getOptions() as $key => $value) {
	    switch (strtolower($key)) {
		case 'modulecontrollerdirectoryname':
		    $front->setModuleControllerDirectoryName($value);
		    break;

		case 'moduledirectory':
		    if (is_array($value)) {
			foreach ($value as $path) {
			    $dir = new DirectoryIterator($path);
			    foreach ($dir as $file) {
				if ($file->isDot() || !$file->isDir() || substr($file->getFilename(), 0, 1) === '.') {
				    continue;
				}
				$front->addModuleDirectory($file->getPathname());
			    }
			}
			unset($this->_options['moduleDirectory']);
		    }
		    break;
	    }
	}
	return parent::init();
    }

}