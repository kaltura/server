<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters
 */
class KalturaAdCuePointFilter extends KalturaAdCuePointBaseFilter
{
	private $map_between_objects = array
	(
		"providerTypeEqual" => "_eq_sub_type",
		"providerTypeIn" => "_in_sub_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * 
	 * 
	 * @var KalturaAdCuePointProviderType
	 */
	public $providerTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaAdCuePointProviderType
	 * @var string
	 */
	public $providerTypeIn;
}
