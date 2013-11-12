<?php

/**
 * @package plugins.widevine
 * @subpackage model
 */
class WidevineFlavorAsset extends flavorAsset
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->type = WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR);
	}
	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE = 'widevine_distribution_start_date';
	const CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE = 'widevine_distribution_end_date';
	const CUSTOM_DATA_WIDEVINE_ASSET_ID = 'widevine_asset_id';
	
	/**
	 * @return int
	 */
	public function getWidevineDistributionStartDate()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE);
	}
	
	public function setWidevineDistributionStartDate($date)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE, $date);
	}
	
	/**
	 * @return int
	 */
	public function getWidevineDistributionEndDate()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE);
	}
	
	public function setWidevineDistributionEndDate($date)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE, $date);
	}
	
	/**
	 * @return int
	 */
	public function getWidevineAssetId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_ASSET_ID);
	}
	
	public function setWidevineAssetId($id)
	{
		$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_ASSET_ID, $id);
	}
	
	public function linkFromAsset(asset $fromAsset)
	{
		parent::linkFromAsset($fromAsset);
		$this->setWidevineAssetId($fromAsset->getWidevineAssetId());
	}
}