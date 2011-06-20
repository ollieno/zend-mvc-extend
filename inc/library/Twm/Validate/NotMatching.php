<?php
require_once 'Zend/Validate/Abstract.php';

class Twm_Validate_NotMatching extends Zend_Validate_Abstract
{
    public $_confirmelementname;
    
    /**
     * Validate if the elementname of $confirmfield has the same value as 
     * the element this validator validates
     * 
     * @param $confirmfield
     * @return string
     */
    public function __construct($confirmfield)
    {
        $this->_confirmelementname = $confirmfield;    
    }
    
    const NOT_MATCH = 'notMatch';
    
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Field values do not match'
    );

    public function isValid($value, $context = null)
    {       
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context[$this->_confirmelementname])
                && ($value == $context[$this->_confirmelementname]))
            {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}