<?php
class Twm_Validate_EmailExists extends Zend_Validate_Abstract
{
    const EXISTS = 'emailExists';
        
    protected $_messageTemplates = array(
        self::EXISTS    => "'%value%' is een reeds geregistreerd e-mail adres"
    );    
    
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        
        $tbl = new DbTable_Account();
        $select = $tbl->select()->where('username = ?', $value);
        $row = $tbl->fetchRow($select);

        if ($row === null)
        {
            return true;
        }

        $this->_error(self::EXISTS);
        return false;        
    }
}