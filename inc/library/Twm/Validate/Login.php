<?php

class Twm_Validate_Login extends Zend_Validate_Abstract
{
    const UNKNOWN = 'unknown';

    protected $_messageTemplates = array(
        self::UNKNOWN => "No account found with the given credentials"
    );

    public function isValid($value)
    {
    	$isValid = true;
    	
        $valueString = (string) $value;
        $this->_setValue($valueString);
        
        $tbl = new DbTable_Account();
        $Account = Service_Auth::getAccount();
        $select = $tbl->select()->where('username = ?', $Account->username);
        $select->where('password = ?', md5($value));
        $row = $tbl->fetchRow($select);
        if ($row === null)
        {
            $isValid = false;
            $this->_error(self::UNKNOWN);
        }

        return $isValid;
    }
}