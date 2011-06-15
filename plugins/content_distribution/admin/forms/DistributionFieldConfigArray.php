<?php

class Form_DistributionFieldConfigArray_SubForm extends Zend_Form_SubForm
{
	public function init()
	{
	    $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
	}
	
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$fieldConfigArray = $this->getFieldConfigArray($properties);
		return $fieldConfigArray;
	}

	
	public function getFieldConfigArray($properties)
	{
	    $fieldConfigArray = array();
	    foreach ($properties as $key => $valueArray)
	    {
	        if (!is_array($valueArray)) {
	            continue;
	        }
	        $tempSubForm = new Form_DistributionFieldConfig_SubForm();
	        $fieldConfig = $tempSubForm->getObject("Kaltura_Client_ContentDistribution_Type_DistributionFieldConfig", $valueArray, false, true);
	        $fieldConfigArray[] = $fieldConfig;
	    }
	    return $fieldConfigArray;
	}
	
	public function addFieldConfigArray($fieldConfigArray)
	{
	    $element = new Zend_Form_Element_Hidden('lableFieldConfigArray');
		$element->setLabel('Field configurations');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($element);
		
		foreach ($fieldConfigArray as $fieldConfig)
		{
		    $fieldName = $fieldConfig->fieldName;
		    $subForm = new Form_DistributionFieldConfig_SubForm();
		    $subForm->populateFromObject($fieldConfig, false);
		    
    		$subForm->removeDecorator('Fieldset');
    		$subForm->removeDecorator('DtDdWrapper');
    		$subForm->removeDecorator('label');
    		$subForm->addDecorator( 'HtmlTag', array('tag' => 'tr'));
    		$this->addSubForm($subForm, $fieldName);
		}
	}

	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
        
	    $fieldConfigArray = array();
	    $subForms = $this->getSubForms();
	    foreach ($subForms as $subForm)
	    {
	        $name = $subForm->getName();;
	        $this->removeSubForm($name);      
	    }
	   
	    $this->addFieldConfigArray($object->fieldConfigArray);
	}	
	
}