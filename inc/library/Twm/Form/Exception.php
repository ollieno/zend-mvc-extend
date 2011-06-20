<?php
class Twm_Form_Exception extends Exception
{
    public $errors;
    public $messages;

    function __construct(Twm_Form_Abstract $form)
    {
	parent::__construct("Form validation failed");
	$this->errors = $form->getErrors ();
	$this->messages = $form->getMessages ();
    }

    function getExtDirectErrors()
    {
	$result = array();
	foreach ($this->messages as $field => $errors)
	{
	    if (count($errors)>0)
	    {
		$result[$field] = implode("\n", $errors);
	    }
	}
	return $result;
    }
}