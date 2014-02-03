<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaDeliveryRtmpBaseFilter extends KalturaDeliveryFilter
{
	static private $map_between_objects = array
	(
		"enforceRtmpeEqual" => "_eq_enforce_rtmpe",
		"enforceRtmpeIn" => "_in_enforce_rtmpe",
		"prefixEqual" => "_eq_prefix",
		"prefixIn" => "_in_prefix",
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
	 * @var KalturaNullableBoolean
	 */
	public $enforceRtmpeEqual;

	/**
	 * @var string
	 */
	public $enforceRtmpeIn;

	/**
	 * @var string
	 */
	public $prefixEqual;

	/**
	 * @var string
	 */
	public $prefixIn;
}
