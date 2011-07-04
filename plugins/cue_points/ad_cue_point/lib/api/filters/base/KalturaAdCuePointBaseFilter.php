<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaAdCuePointBaseFilter extends KalturaCuePointFilter
{
	private $map_between_objects = array
	(
		"providerTypeEqual" => "_eq_provider_type",
		"providerTypeIn" => "_in_provider_type",
		"adTypeEqual" => "_eq_ad_type",
		"adTypeIn" => "_in_ad_type",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
	);

	private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
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

	/**
	 * 
	 * 
	 * @var KalturaAdType
	 */
	public $adTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaAdType
	 * @var string
	 */
	public $adTypeIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endTimeLessThanOrEqual;
}
