<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaGenericDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaGenericDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaGenericDistributionProfileBaseFilter::$order_by_map);
	}
}
