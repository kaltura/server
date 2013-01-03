<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaThumbParamsOutputBaseFilter extends KalturaThumbParamsFilter
{
	static private $map_between_objects = array
	(
		"thumbParamsIdEqual" => "_eq_thumb_params_id",
		"thumbParamsVersionEqual" => "_eq_thumb_params_version",
		"thumbAssetIdEqual" => "_eq_thumb_asset_id",
		"thumbAssetVersionEqual" => "_eq_thumb_asset_version",
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
	public $thumbParamsIdEqual;

	/**
	 * @var string
	 */
	public $thumbParamsVersionEqual;

	/**
	 * @var string
	 */
	public $thumbAssetIdEqual;

	/**
	 * @var string
	 */
	public $thumbAssetVersionEqual;
}
