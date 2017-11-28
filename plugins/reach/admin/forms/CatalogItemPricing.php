<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemPricing extends Zend_Form_SubForm
{
	public function init()
	{
 		$this->setLegend("Catalog Item Pricing");
 		$this->setName("catalogItemPricing");

 		$this->addElement('select', 'objectType', array(
 				'label'			=> 'Catalog Pricing Type:',
 				'filters'		=> array('StringTrim'),
 				'multiOptions'  => array(),
 		));
		$this->addElement('text', 'PricePerUnit', array(
			'label'			=> 'Price Per Unit:',
			'filters'		=> array('StringTrim'),
		));		
		
		$this->addElement('text', 'PriceFunction', array(
				'label'			=> 'Price Function:',
				'filters'		=> array('StringTrim'),
		));
	}
	
	public function updateCatalogItemPricingOptions(array $options) {
		$this->getElement("objectType")->setAttrib('catalogItemPricingOptions', $options);
	}
	
	
	public function populateFromObject($recognizer, $add_underscore = false)
	{
		$this->getElement("objectType")->setValue(get_class($recognizer));
		
		if(is_null($recognizer))
			return;
		
		$props = $recognizer;
		if(is_object($recognizer))
			$props = get_object_vars($recognizer);
		
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
	
	public function getObject($properties) {
		$objectClass = $properties["objectType"];
		if($objectClass == "Null") 
			return KalturaNull::getInstance();
		
		$object = new $objectClass();
	
		foreach($properties as $prop => $value) {
			if($prop == "objectType")
				continue;
			$object->$prop = $value;
		}
	
		return $object;
	}
}