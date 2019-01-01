<?php
/**
 * @package plugins.videoIndexerDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVideoIndexerDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaVideoIndexerDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaVideoIndexerDistributionProfileBaseFilter::$order_by_map);
	}
}
