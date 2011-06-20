<?php

class Twm_Filter_Tags implements Zend_Filter_Interface {

    public function filter($value)
    {
		$words = explode(',', $value);
		$filterChain = new Zend_Filter();
		$filterChain->addFilter(new Zend_Filter_StringTrim())
			->addFilter(new Zend_Filter_StringToLower())
			->addFilter(new Zend_Filter_StripNewlines())
			->addFilter(new Zend_Filter_Alnum(array('allowwhitespace' => true)));

		$filterd = array();
		foreach($words as $word)
		{
			$word = $filterChain->filter($word);
			if (strlen($word)>0)
			{
				$filterd[]=$word;
			}
		}
		return implode(',', $filterd);
    }
}