<?php

class Twm_Page_Block_Html_Tabpanel extends Twm_Core_Block_Template {

	function  beforeToHtml() {
		parent::beforeToHtml();
		$this->_view->selectedtab = $this->_request->getParam('tabindex', 1);
	}

}