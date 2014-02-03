<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaDeliveryAkamaiRtspBaseFilter extends KalturaDeliveryFilter
{
	static private $map_between_objects = array
	(
		"cpCodeEqual" => "_eq_cp_code",
		"cpCodeIn" => "_in_cp_code",
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
	public $cpCodeEqual;

	/**
	 * @var string
	 */
	public $cpCodeIn;
}
