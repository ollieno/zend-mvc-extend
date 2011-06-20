<?php

class Twm_View_Helper_Url extends Zend_View_Helper_Url
{
	function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {
		if (!empty($urlOptions['name']))
		{
			$urlOptions['name'] = $this->slug($urlOptions['name']);
		}
		return parent::url($urlOptions, $name, $reset, $encode);
	}
	
	protected function slug($str) {
		$str = strtolower(trim($str));
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = preg_replace('/-+/', "-", $str);
		return $str;
	}	
}