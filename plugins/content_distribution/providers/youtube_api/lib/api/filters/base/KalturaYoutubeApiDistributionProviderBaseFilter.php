<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaYoutubeApiDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaYoutubeApiDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaYoutubeApiDistributionProviderBaseFilter::$order_by_map);
	}
}
