<?php
/**
 * @package Admin
 * @subpackage forms
 */
class Kaltura_Form_Element_EnumSelect extends Zend_Form_Element_Select
{
	function __construct($spec, $options = null, $additionalValues = array())
	{
		parent::__construct($spec, $options);
		
		if (!isset($options['enum']))
			throw new Zend_Form_Exception('Please specify the enum type');
		 
		if(!class_exists($options['enum']))
			throw new Zend_Form_Exception('Enum type does not exists');
			
		$enumName = $options['enum'];
		
		$oClass = new ReflectionClass($enumName);
		$constants = $oClass->getConstants();


		foreach ($additionalValues  as $key => $value)
			$this->addMultiOption($key, $value);

		foreach($constants as $constName => $constValue)
		{
			if(isset($options['excludes']) && in_array($constValue, $options['excludes']))
				continue;
				
			$this->addMultiOption($constValue, "$enumName::$constName");
		}
	}
}
