<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaDistributionProviderBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
	);

	private $order_by_map = array
	(
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
	 * @var KalturaDistributionProviderType
	 */
	public $typeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaDistributionProviderType
	 * @var string
	 */
	public $typeIn;
}
