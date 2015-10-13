<?php 
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
abstract class Form_ConfigurableProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function init ()
	{
		parent::init();
		
		$this->addElement('checkbox', 'use_category_entries', array(
			'label' => 'Query category entries',
			'isArray' => true,
		));
		
		$this->addDisplayGroup(array ('use_category_entries'), 
		'configurable_distribution_profile_general_settings',
		array(
				'legend' => 'Configurable Distribution Profile- General Settings',
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag', array('class' => "configurable_distribution_profile_general_settings"))),
			));
		
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$fieldConfigArray = isset($properties['fieldConfigArray']) ? $properties['fieldConfigArray'] : array();		
		$object->fieldConfigArray = $this->getFieldConfigArray($fieldConfigArray);
		
		$itemXpathsToExtend = isset($properties['itemXpathsToExtend']) && is_array($properties['itemXpathsToExtend']) ? $properties['itemXpathsToExtend'] : array();
		$object->itemXpathsToExtend = array();
		$categoryFeaturesToExtend = array();
		foreach($itemXpathsToExtend as $key => $val)
		{
			if ((string)$key == 'includeCategoryInMrss'){
				$categoryFeaturesToExtend[] = Kaltura_Client_Enum_ObjectFeatureType::METADATA;
				continue;
			}
			else if ((string)$key == 'includeCategoryParentInMrss'){
				$categoryFeaturesToExtend[] = Kaltura_Client_Enum_ObjectFeatureType::ANCESTOR_RECURSIVE;
				continue;
			}
			else if ($val){	
				$temp = new Kaltura_Client_Type_ExtendingItemMrssParameter();
				$temp->xpath = $val;
				$temp->identifier = new Kaltura_Client_Type_EntryIdentifier();
				$temp->identifier->identifier = Kaltura_Client_Enum_EntryIdentifierField::ID;
				$temp->identifier->extendedFeatures = "";
				$temp->extensionMode = Kaltura_Client_Enum_MrssExtensionMode::APPEND;
				$object->itemXpathsToExtend [] = $temp;
			}
		}
		
		if (count($categoryFeaturesToExtend)){
			$categoryFeaturesToExtend[] = Kaltura_Client_Enum_ObjectFeatureType::CUSTOM_DATA;
			$temp = new Kaltura_Client_Type_ExtendingItemMrssParameter();
			$temp->xpath = '//category';
			$temp->identifier = new Kaltura_Client_Type_CategoryIdentifier();
			$temp->identifier->identifier = Kaltura_Client_Enum_CategoryIdentifierField::FULL_NAME;
			$temp->identifier->extendedFeatures = implode(',', $categoryFeaturesToExtend);
			$temp->extensionMode = Kaltura_Client_Enum_MrssExtensionMode::REPLACE;
			$object->itemXpathsToExtend [] = $temp;
		}
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
		$extendCategory = false;
		$extendParentCategory = false;
		
		foreach($itemXpathsToExtend as $itemXPath)
		{
			/* @var $itemXPath Kaltura_Client_Type_ExtendingItemMrssParameter */
			//if it a category identifier
			if ($itemXPath->identifier instanceof Kaltura_Client_Type_CategoryIdentifier){
				/* @var $identifier Kaltura_Client_Type_CategoryIdentifier */
				$identifier = $itemXPath->identifier;
				//if the parameters are set exactly as the admin console sets.
				if ($itemXPath->xpath == '//category' && $itemXPath->extensionMode == Kaltura_Client_Enum_MrssExtensionMode::REPLACE
					&& $identifier->identifier == Kaltura_Client_Enum_CategoryIdentifierField::FULL_NAME){
					foreach (explode(',', $identifier->extendedFeatures) as $extendedFeature){
						if ($extendedFeature == Kaltura_Client_Enum_ObjectFeatureType::METADATA){
							$extendCategory = true;
						}
						elseif ($extendedFeature == Kaltura_Client_Enum_ObjectFeatureType::ANCESTOR_RECURSIVE){
							$extendParentCategory = true;
						}
					}
				}
				continue;
			}
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
		
		//set the extend category metadata checkbox
		$subForm = new Zend_Form_SubForm(array('disableLoadDefaultDecorators' => true));
		$subForm->setDecorators(array(
				'FormElements',
			));
		$subForm->addElement('checkbox', 'includeCategoryInMrss', array(
			'label' => 'Include category-level custom metadata in MRSS',
			'isArray' => true,
			'value' => $extendCategory,
		));
		$subForm->getElement('includeCategoryInMrss')->getDecorator('Label')->setOption('placement', 'APPEND');
		$subForm->getElement('includeCategoryInMrss')->setChecked($extendCategory);
		$mainSubForm->addSubForm($subForm, 'itemXpathsToExtend_subform_'.$i++,99);

		//set the extend category parent metadata checkbox
		$subForm = new Zend_Form_SubForm(array('disableLoadDefaultDecorators' => true));
		$subForm->setDecorators(array(
				'FormElements',
			));
		$subForm->addElement('checkbox', 'includeCategoryParentInMrss', array(
			'label' => 'Include parent categories',
			'isArray' => true,
			'value' => $extendParentCategory,
		));
		
		$subForm->getElement('includeCategoryParentInMrss')->getDecorator('Label')->setOption('placement', 'APPEND');
		$subForm->getElement('includeCategoryParentInMrss')->setChecked($extendParentCategory);
		$mainSubForm->addSubForm($subForm, 'itemXpathsToExtend_subform_'.$i++,100);
		
		$this->addSubForm($mainSubForm, 'itemXpathsToExtend_group');
	}
}
