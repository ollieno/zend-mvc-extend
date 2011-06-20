<?php
class Twm_Form_DbTable_Abstract extends Twm_Form_Abstract
{
    protected $_DbTable = null;

    function __construct($DbTable , $options = null)
    {
	if (!is_array($DbTable))
	{
	    $this->_DbTable = array($DbTable);
	}
	else
	{
	    $this->_DbTable = $DbTable;
	}

	parent::__construct($options);
    }

    function init()
    {
	foreach ($this->_DbTable as $DbTable)
	{
	    $meta = $DbTable->info('metadata');
	    foreach ($meta as $column)
	    {
		switch (strtolower($column['DATA_TYPE']))
		{
		    case 'int':
			$this->addElementInt($column);
			break;
		    case 'varchar':
			$this->addElementVarchar($column);
			break;
		    case 'text':
			$this->addElementText($column);
			break;
		    case 'date':
			$this->addElementDate($column);
			break;
		}
	    }
	}
    }

    private function addDefaultValidators(Zend_Form_Element $element, $meta)
    {
	if (!$meta['NULLABLE'] && $meta['PRIMARY'] != true)
	{
	    $element->setRequired(true);
	    $element->setAllowEmpty(false);
	}
    }

    private function addElementInt($meta)
    {
	$element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
	$this->addDefaultValidators($element, $meta);
	$this->addElement($element);
    }
    private function addElementVarchar($meta)
    {
	$element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
	$this->addDefaultValidators($element, $meta);
	$this->addElement($element);
    }
    private function addElementText($meta)
    {
	$element = new Zend_Form_Element_Textarea($meta['COLUMN_NAME']);
	$this->addDefaultValidators($element, $meta);
	$this->addElement($element);
    }
    private function addElementDate($meta)
    {
	$element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
	$this->addDefaultValidators($element, $meta);
	$this->addElement($element);

    }
}