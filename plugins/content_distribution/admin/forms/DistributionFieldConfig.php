<?php

class Form_DistributionFieldConfig_SubForm extends Zend_Form_SubForm
{
	public function init()
	{
		$this->setDecorators(array(
			'FormElements',
			array('ViewScript', array(
				'viewScript' => 'distribution-field-config-sub-form.phtml',
				'placement' => false
			))
		));
		
		$this->addElement('checkbox', 'override', array(
			'filters' 		=> array('StringTrim'),
			'class' 		=> 'override',
			'decorators'    => array('ViewHelper'),
		));

		// we add fieldName as 2 fields (1 hidden, 1 disabled text) because 
		// disabled fields are not sent by the browser when the form is submitted 
		$this->addElement('text', 'fieldNameForView', array(
			'filters' 		=> array('StringTrim'),
			'disabled'		=> true,
			'class'			=> 'field-name',
			'decorators'    => array('ViewHelper'),
		));
		
		$this->addElement('hidden', 'fieldName', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper'),
		));
	
		$this->addElement('text', 'userFriendlyFieldName', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Name:',
		));	
		
		$this->addElement('text', 'entryMrssXslt', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'MRSS XSLT:',
		));
		
		$this->addElement('checkbox', 'isRequired', array(
			'filters'		=> array('StringTrim'),
			'label'			=> 'Required:',
		));
		
		$this->addElement('checkbox', 'updateOnChange', array(
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper'),
			'class' 		=> 'update-on-change',
			'label' 		=> 'Trigger Update:', 
		));
		
		$this->addElement('text', 'updateParam', array(
			'filters'		=> array('StringTrim'),
			'class'			=> 'update-param',
			'decorators'	=> array('ViewHelper'),
		));
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);
			
		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/'; 
				$replacement = '\1_\2'; 
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$this->setDefault($prop, $value);
		}
		
		$this->setDefault('fieldNameForView', $object->fieldName);
		
		if (!$object->isDefault)
			$this->setDefault('override', true);
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = new $objectType;
		foreach($properties as $prop => $value)
		{
			if($add_underscore)
			{
				$parts = explode('_', strtolower($prop));
				$prop = '';
				foreach ($parts as $part) 
					$prop .= ucfirst(trim($part));
				$prop[0] = strtolower($prop[0]);
			}

			if ($value !== '' || $include_empty_fields)
			{
				try{
					$object->$prop = $value;
				}catch(Exception $e){}
			}
		}
		
		return $object;
	}
}