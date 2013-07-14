<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class Form_DistributionFieldConfigArray_SubForm extends Zend_Form_SubForm
{
	public function init()
	{
		$this->setLegend('Field configurations');
	    $this->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('ViewScript', array(
               'viewScript' => 'distribution-field-config-array-sub-form.phtml',
               'placement' => false
             ))
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
			if (!is_array($valueArray))
				continue;
			
			if (!isset($valueArray['override']) || !$valueArray['override'])
				continue;
			
			$tempSubForm = new Form_DistributionFieldConfig_SubForm();
			$fieldConfig = $tempSubForm->getObject("Kaltura_Client_ContentDistribution_Type_DistributionFieldConfig", $valueArray, false, true);
			$fieldConfigArray[] = $fieldConfig;
		}
		return $fieldConfigArray;
	}
	
	public function addFieldConfigArray($fieldConfigArray)
	{
		foreach ($fieldConfigArray as $fieldConfig)
		{
			$fieldName = $fieldConfig->fieldName;
			$subForm = new Form_DistributionFieldConfig_SubForm();
			$subForm->populateFromObject($fieldConfig, false);
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