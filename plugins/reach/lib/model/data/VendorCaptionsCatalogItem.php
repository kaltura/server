<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *ac
 * @package plugins.reach
 * @subpackage model
 */
class VendorCaptionsCatalogItem extends VendorCatalogItem 
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::CAPTIONS);
	}
	
	const CUSTOM_DATA_ENABLE_SPEAKER_ID = "enable_speaker_id";
	const CUSTOM_DATA_FIXED_PRICE_ADDONS = "fixed_price_addons";
	
	
	public function setEnableSpeakerId($enableSpeakerId)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_ID, $enableSpeakerId);
	}
	
	public function setFixedPriceAddons($fixedPriceAddons)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIXED_PRICE_ADDONS, $fixedPriceAddons);
	}
	
	public function getEnableSpeakerId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_ID);
	}
	
	public function getFixedPriceAddons()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIXED_PRICE_ADDONS, null, 0);
	}
	
	public function calculateTaskPrice($entryObject, $entryObjectType, $taskData, $unitsForPricing = null)
	{
		$taskPrice = parent::calculateTaskPrice($entryObject, $entryObjectType, $taskData, $unitsForPricing);
		$taskPrice += $this->getFixedPriceAddons();
		return $taskPrice;
	}

} // VendorCaptionsCatalogItem
