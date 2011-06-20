<?php

class Twm_Core_Block_Reference extends Twm_Core_Model_Abstract {

	function setActions($value)
	{
		$name = $this->getReference();
		$Block = Twm::getLayout()->getBlock($name);
		foreach ($value as $method => $params)
		{
			$Block->$method($params);
		}
	}
}