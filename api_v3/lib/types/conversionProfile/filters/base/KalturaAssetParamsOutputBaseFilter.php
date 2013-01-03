<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaAssetParamsOutputBaseFilter extends KalturaAssetParamsFilter
{
	static private $map_between_objects = array
	(
		"assetParamsIdEqual" => "_eq_asset_params_id",
		"assetParamsVersionEqual" => "_eq_asset_params_version",
		"assetIdEqual" => "_eq_asset_id",
		"assetVersionEqual" => "_eq_asset_version",
		"formatEqual" => "_eq_format",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var int
	 */
	public $assetParamsIdEqual;

	/**
	 * @var string
	 */
	public $assetParamsVersionEqual;

	/**
	 * @var string
	 */
	public $assetIdEqual;

	/**
	 * @var string
	 */
	public $assetVersionEqual;

	/**
	 * @var KalturaContainerFormat
	 */
	public $formatEqual;
}
