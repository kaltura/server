<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaUiConfAdminBaseFilter extends KalturaUiConfFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaUiConfAdminBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaUiConfAdminBaseFilter::$order_by_map);
	}
}
