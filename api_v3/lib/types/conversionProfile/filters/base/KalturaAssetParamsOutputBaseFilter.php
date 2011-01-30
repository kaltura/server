<?php
/**
 * @package 
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaAssetParamsOutputBaseFilter extends KalturaAssetParamsFilter
{
	private $map_between_objects = array
	(
		"assetParamsIdEqual" => "_eq_asset_params_id",
		"assetParamsVersionEqual" => "_eq_asset_params_version",
		"assetIdEqual" => "_eq_asset_id",
		"assetVersionEqual" => "_eq_asset_version",
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
	public $assetParamsIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $assetParamsVersionEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $assetIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $assetVersionEqual;
}
