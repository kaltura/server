<?php
/**
 * @package plugins.tvComDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaTVComDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaTVComDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaTVComDistributionProfileBaseFilter::$order_by_map);
	}
}
