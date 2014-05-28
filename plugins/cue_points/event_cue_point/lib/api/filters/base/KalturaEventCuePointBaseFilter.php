<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaEventCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
	);

	static private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
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
	public $endTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $endTimeLessThanOrEqual;
}
