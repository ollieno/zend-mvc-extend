<?php
ini_set('display_errors', 1);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define ( 'ROOT_PATH', realpath(dirname ( dirname ( __FILE__ ) ) ));
define ( 'APPLICATION_PATH', ROOT_PATH . '/inc/application' );
define ( 'CORE_MODULE_PATH', ROOT_PATH . '/inc/application/code/core/' );
define ( 'LOCAL_MODULE_PATH', ROOT_PATH . '/inc/application/code/local/' );
define ( 'CONFIG_PATH', APPLICATION_PATH . '/config' );
define ( 'DESIGN_PATH', APPLICATION_PATH . '/design' );
define ( 'LIB_PATH', ROOT_PATH . '/inc/library' );
define ( 'DATA_PATH', ROOT_PATH . '/inc/data' );

require_once LIB_PATH. '/Twm/Application.php';
$application = new Twm_Application ( APPLICATION_ENV, CONFIG_PATH . '/application.ini' );
$application->bootstrap ()->run();