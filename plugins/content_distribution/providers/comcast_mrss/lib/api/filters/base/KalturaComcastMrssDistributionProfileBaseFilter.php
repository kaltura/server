<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaComcastMrssDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaComcastMrssDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaComcastMrssDistributionProfileBaseFilter::$order_by_map);
	}
}
