<?php
/**
 * @package 
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaMediaInfoBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"flavorAssetIdEqual" => "_eq_flavor_asset_id",
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
	 * @var string
	 */
	public $flavorAssetIdEqual;
}
