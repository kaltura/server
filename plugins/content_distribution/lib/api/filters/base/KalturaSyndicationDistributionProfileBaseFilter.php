<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSyndicationDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaSyndicationDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaSyndicationDistributionProfileBaseFilter::$order_by_map);
	}
}
