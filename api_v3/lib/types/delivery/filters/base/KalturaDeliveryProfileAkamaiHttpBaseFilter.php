<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaDeliveryProfileAkamaiHttpBaseFilter extends KalturaDeliveryProfileFilter
{
	static private $map_between_objects = array
	(
		"useIntelliseekEqual" => "_eq_use_intelliseek",
		"useIntelliseekIn" => "_in_use_intelliseek",
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
	public $useIntelliseekEqual;

	/**
	 * @var string
	 */
	public $useIntelliseekIn;
}
