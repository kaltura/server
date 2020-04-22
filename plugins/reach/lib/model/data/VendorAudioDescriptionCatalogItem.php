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
class VendorAudioDescriptionCatalogItem extends VendorCatalogItem
{
	const CUSTOM_DATA_FLAVOR_PARAMS_ID = "flavor_params_id";
	const CUSTOM_DATA_CLEAR_AUDIO_FLAVOR_PARAMS_ID = "clear_audio_flavor_params_id";
	
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::AUDIO_DESCRIPTION);
	}
	
	public function setFlavorParamsId($flavorParamsId)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FLAVOR_PARAMS_ID, $flavorParamsId);
	}
	
	public function setClearAudioFlavorParamsId($clearAudioFlavorParamsId)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CLEAR_AUDIO_FLAVOR_PARAMS_ID, $clearAudioFlavorParamsId);
	}
	
	public function getFlavorParamsId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FLAVOR_PARAMS_ID);
	}
	
	public function getClearAudioFlavorParamsId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CLEAR_AUDIO_FLAVOR_PARAMS_ID);
	}
	
} // VendorAudioDescriptionCatalogItem
