<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_VendorCatalogItemPricing extends ConfigureSubForm
{
	public function init()
	{
 		$this->setName("catalogItemPricing");
		$this->addElement('text', 'pricePerUnit', array(
			'label'			=> 'Price Per Unit*:',
			'required'		=> true,
			'validators'	=> array('Float'),
		));
		
		$priceFunction = new Kaltura_Form_Element_EnumSelect('priceFunction', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction'));
		$priceFunction->setRequired(true);
		$priceFunction->setLabel("Price Function*:");
		$priceFunction->setValue(Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_MINUTE);
		$this->addElement($priceFunction);
	}
	
	
	public function populateFromObject($pricing)
	{
		//If user does not have permissions to access the pricing data this will cause PHP notices
		if($pricing)
		{
			$this->setDefault('pricePerUnit',  $pricing->pricePerUnit);
			$this->setDefault('priceFunction',  $pricing->priceFunction);
		}
	}
	
	public function getObject($properties) 
	{
		$pricingObject = new Kaltura_Client_Reach_Type_VendorCatalogItemPricing();
		$pricingObject->pricePerUnit = $properties['pricePerUnit'];
		$pricingObject->priceFunction = $properties['priceFunction'];
		return $pricingObject;
	}
}