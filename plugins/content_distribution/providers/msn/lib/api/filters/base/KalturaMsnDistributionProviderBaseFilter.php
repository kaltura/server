<?php
/**
 * @package plugins.msnDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaMsnDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaMsnDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaMsnDistributionProviderBaseFilter::$order_by_map);
	}
}
