<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaThumbAssetBaseFilter extends KalturaAssetFilter
{
	static private $map_between_objects = array
	(
		"thumbParamsIdEqual" => "_eq_thumb_params_id",
		"thumbParamsIdIn" => "_in_thumb_params_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
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
	public $thumbParamsIdIn;

	/**
	 * @var KalturaThumbAssetStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $statusNotIn;
}
