<?php 
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
abstract class Form_ConfigurableProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$fieldConfigArray = isset($properties['fieldConfigArray']) ? $properties['fieldConfigArray'] : array();		
		$object->fieldConfigArray = $this->getFieldConfigArray($fieldConfigArray);
		
		$itemXpathsToExtend = isset($properties['itemXpathsToExtend']) && is_array($properties['itemXpathsToExtend']) ? $properties['itemXpathsToExtend'] : array();
		foreach($itemXpathsToExtend as &$val)
		{
			$temp = new Kaltura_Client_Type_ExtendingItemMrssParameter();
			$temp->xpath = $val;
			$temp->identifier = new Kaltura_Client_Type_EntryIdentifier();
			$temp->identifier->extendedFeatures = "";
			$temp->extensionMode = Kaltura_Client_Enum_MrssExtensionMode::APPEND;
			$val = $temp;
		}
		$object->itemXpathsToExtend = $itemXpathsToExtend;
		
		return $object;
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		$this->addFieldConfigArray($object->fieldConfigArray);
		
		$this->addItemXpathsToExtend($object->itemXpathsToExtend);
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
	
	protected function addItemXpathsToExtend($itemXpathsToExtend)
	{
		if (count($itemXpathsToExtend) == 0)
			$itemXpathsToExtend = array(new Kaltura_Client_Type_ExtendingItemMrssParameter());
			
		$mainSubForm = new Zend_Form_SubForm();
		$mainSubForm->setLegend('Item XPaths To Extend');
		$mainSubForm->setDecorators(array(
			'FormElements',
			array('ViewScript', array(
				'viewScript' => 'distribution-item-xpath-to-extend.phtml',
				'placement' => 'APPEND'
			)),
			'Fieldset'
		));
		
		$i = 1;
		foreach($itemXpathsToExtend as $itemXPath)
		{
			/* @var $itemXPath Kaltura_Client_Type_ExtendingItemMrssParameter */
			$subForm = new Zend_Form_SubForm(array('disableLoadDefaultDecorators' => true));
			$subForm->setDecorators(array(
				'FormElements',
			));
			$subForm->addElement('text', 'itemXpathsToExtend', array(
				'decorators' => array('ViewHelper', array('HtmlTag', array('tag' => 'div'))),
				'isArray' => true,
				'value' => $itemXPath->xpath
			));
			
			$mainSubForm->addSubForm($subForm, 'itemXpathsToExtend_subform_'.$i++);
		}
		
		$this->addSubForm($mainSubForm, 'itemXpathsToExtend_group');
	}
}
