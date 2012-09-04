<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaQuickPlayDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaQuickPlayDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaQuickPlayDistributionProfileBaseFilter::$order_by_map);
	}
}
