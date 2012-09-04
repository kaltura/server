<?php
/**
 * @package plugins.huluDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaHuluDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaHuluDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaHuluDistributionProviderBaseFilter::$order_by_map);
	}
}
