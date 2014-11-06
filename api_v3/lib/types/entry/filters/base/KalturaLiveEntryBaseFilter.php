<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaLiveEntryBaseFilter extends KalturaMediaEntryFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
		"+firstBroadcast" => "+first_broadcast",
		"-firstBroadcast" => "-first_broadcast",
		"+lastBroadcast" => "+last_broadcast",
		"-lastBroadcast" => "-last_broadcast",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
}
