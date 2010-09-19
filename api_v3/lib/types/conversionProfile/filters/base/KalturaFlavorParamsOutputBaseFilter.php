<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsFilter
{
	private $map_between_objects = array
	(
		"flavorParamsIdEqual" => "_eq_flavor_params_id",
		"flavorParamsVersionEqual" => "_eq_flavor_params_version",
		"flavorAssetIdEqual" => "_eq_flavor_asset_id",
		"flavorAssetVersionEqual" => "_eq_flavor_asset_version",
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
	 * @var int
	 */
	public $flavorParamsIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $flavorParamsVersionEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $flavorAssetIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $flavorAssetVersionEqual;
}
