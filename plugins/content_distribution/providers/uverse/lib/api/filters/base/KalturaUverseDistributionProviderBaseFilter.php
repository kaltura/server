<?php
/**
 * @package plugins.uverseDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaUverseDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaUverseDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaUverseDistributionProviderBaseFilter::$order_by_map);
	}
}
