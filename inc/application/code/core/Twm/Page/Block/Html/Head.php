<?php

class Twm_Page_Block_Html_Head extends Twm_Core_Block_Template {

	function addMeta() {
		$this->getView();
		$args = func_get_args();
		$function = array_shift($args);
		call_user_func_array(array($this->_view->headMeta(), $function), $args);
	}

	function addCss() {
		$this->getView();
		$args = func_get_args();
		$function = array_shift($args);
		$args[0] = $this->_view->skinUrl($args[0]);
		call_user_func_array(array($this->_view->headLink(), $function), $args);
	}

	function addScript() {
		$this->getView();
		$args = func_get_args();
		$function = array_shift($args);
		$args[0] = $this->_view->skinUrl($args[0]);
		call_user_func_array(array($this->_view->headScript(), $function), $args);
	}
}