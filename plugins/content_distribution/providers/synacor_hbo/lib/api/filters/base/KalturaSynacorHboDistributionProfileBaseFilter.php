<?php
/**
 * @package plugins.synacorHboDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSynacorHboDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaSynacorHboDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaSynacorHboDistributionProfileBaseFilter::$order_by_map);
	}
}
