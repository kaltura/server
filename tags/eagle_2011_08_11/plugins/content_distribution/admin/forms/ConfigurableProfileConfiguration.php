<?php 
abstract class Form_ConfigurableProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$fieldConfigArray = isset($properties['fieldConfigArray']) ? $properties['fieldConfigArray'] : array();		
		$object->fieldConfigArray = $this->getFieldConfigArray($fieldConfigArray);
		return $object;
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
	    $this->addFieldConfigArray($object->fieldConfigArray);
	}	
	
	protected function getFieldConfigArray($fieldConfigArray)
	{
	    $fieldConfigArraySubForm = new Form_DistributionFieldConfigArray_SubForm();
	    $fieldConfigArray = $fieldConfigArraySubForm->getFieldConfigArray($fieldConfigArray);
	    return $fieldConfigArray;
	}
	
	protected function addFieldConfigArray($fieldConfigArray)
	{
	    $fieldConfigArraySubForm = new Form_DistributionFieldConfigArray_SubForm();
	    $fieldConfigArraySubForm->addFieldConfigArray($fieldConfigArray);
	    $this->addSubForm($fieldConfigArraySubForm, 'fieldConfigArray');
	}
}
