<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSyndicationDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaSyndicationDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaSyndicationDistributionProviderBaseFilter::$order_by_map);
	}
}
