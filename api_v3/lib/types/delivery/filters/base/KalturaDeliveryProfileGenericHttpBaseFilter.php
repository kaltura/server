<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaDeliveryProfileGenericHttpBaseFilter extends KalturaDeliveryProfileFilter
{
	static private $map_between_objects = array
	(
		"patternEqual" => "_eq_pattern",
		"patternIn" => "_in_pattern",
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
	 * @var string
	 */
	public $patternEqual;

	/**
	 * @var string
	 */
	public $patternIn;
}
