<?php
class Kaltura_Form_Element_EnumSelect extends Zend_Form_Element_Select
{
	function __construct($spec, $options = null)
	{
		parent::__construct($spec, $options);
		
		if (!isset($options['enum']))
			throw new Zend_Form_Exception('Please specify the enum type');
		 
		if(!class_exists($options['enum']))
			throw new Zend_Form_Exception('Enum type does not exists');
			
		$enumName = $options['enum'];
		
		$oClass = new ReflectionClass($enumName);
		$constants = $oClass->getConstants();
		
		foreach($constants as $constName => $constValue)
		{
			$this->addMultiOption($constValue, "$enumName::$constName");
		}
	}
}