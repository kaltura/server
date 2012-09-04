<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaNotificationBaseFilter extends KalturaBaseJobFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaNotificationBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaNotificationBaseFilter::$order_by_map);
	}
}
