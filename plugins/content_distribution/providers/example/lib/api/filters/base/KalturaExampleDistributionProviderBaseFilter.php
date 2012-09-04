<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaExampleDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaExampleDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaExampleDistributionProviderBaseFilter::$order_by_map);
	}
}
