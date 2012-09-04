<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaUverseClickToOrderDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaUverseClickToOrderDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaUverseClickToOrderDistributionProviderBaseFilter::$order_by_map);
	}
}
