<?php
/**
 * @package plugins.widevine
 * @subpackage model
 */
class WidevineFlavorParamsOutput extends flavorParamsOutput
{
	const CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE = 'widevine_distribution_start_date';
	const CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE = 'widevine_distribution_end_date';
	
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
	
	public function setWidevineDistributionStartDate($v)	{$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE, $v);}
	public function getWidevineDistributionStartDate()		{return $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_START_DATE);}
	
	public function setWidevineDistributionEndDate($v)	{$this->putInCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE, $v);}
	public function getWidevineDistributionEndDate()	{return $this->getFromCustomData(self::CUSTOM_DATA_WIDEVINE_DISTRIBUTION_END_DATE);}
	
	
}