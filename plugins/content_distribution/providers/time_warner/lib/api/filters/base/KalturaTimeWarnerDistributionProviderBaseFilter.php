<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaTimeWarnerDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaTimeWarnerDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaTimeWarnerDistributionProviderBaseFilter::$order_by_map);
	}
}
