<?php

class Twm_Controller_Plugin_Page extends Zend_Controller_Plugin_Abstract {

    function routeShutdown(Zend_Controller_Request_Abstract $request) {
	$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
	Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	Zend_Controller_Action_HelperBroker::addHelper(new Twm_Controller_Action_Helper_ViewRenderer($view));
    }

}