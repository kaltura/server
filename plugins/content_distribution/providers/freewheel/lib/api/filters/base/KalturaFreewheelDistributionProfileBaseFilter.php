<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaFreewheelDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaFreewheelDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaFreewheelDistributionProfileBaseFilter::$order_by_map);
	}
}
