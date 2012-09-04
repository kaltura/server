<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaYahooSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaYahooSyndicationFeedBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaYahooSyndicationFeedBaseFilter::$order_by_map);
	}
}
