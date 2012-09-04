<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaAdminUserBaseFilter extends KalturaUserFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaAdminUserBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaAdminUserBaseFilter::$order_by_map);
	}
}
