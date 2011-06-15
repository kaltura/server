<?php

class Form_DistributionFieldConfig_SubForm extends Zend_Form_SubForm
{
	public function init()
	{	    
		$this->addElement('checkbox', 'resetOnSave', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
		));
	    
	    $this->addElement('text', 'fieldName', array(
			'filters' 		=> array('StringTrim'),
			'readonly'		=> true,
			'class'			=> 'readonly',
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
		));		
	
		$this->addElement('text', 'userFriendlyFieldName', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
		));	
		
		$this->addElement('text', 'entryMrssXslt', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
		));
		
		$this->addElement('checkbox', 'updateOnChange', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
		));
		
		$this->addElement('text', 'updateParam', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper', array('HtmlTag', array('tag' => 'td'))),
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