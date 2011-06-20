<?php

class Twm_Page_Block_Html_Flashmessenger extends Twm_Core_Block_Template {

	function  init($data) {
		parent::init($data);
		$this->getView();
		$flashmesenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$flashnotices = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashNotices');
		$flasherrors = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashErrors');
		$this->_view->messages = $flashmesenger->getMessages();
		$this->_view->notices = $flashnotices->getMessages();
		$this->_view->errors = $flasherrors->getMessages();
	}
}