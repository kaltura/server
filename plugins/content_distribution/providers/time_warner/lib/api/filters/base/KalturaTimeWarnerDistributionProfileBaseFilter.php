<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaTimeWarnerDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaTimeWarnerDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaTimeWarnerDistributionProfileBaseFilter::$order_by_map);
	}
}
