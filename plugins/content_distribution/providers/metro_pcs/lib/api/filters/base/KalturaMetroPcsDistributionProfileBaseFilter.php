<?php
/**
 * @package plugins.metroPcsDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaMetroPcsDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaMetroPcsDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaMetroPcsDistributionProfileBaseFilter::$order_by_map);
	}
}
