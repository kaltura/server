<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaGenericSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaGenericSyndicationFeedBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaGenericSyndicationFeedBaseFilter::$order_by_map);
	}
}
