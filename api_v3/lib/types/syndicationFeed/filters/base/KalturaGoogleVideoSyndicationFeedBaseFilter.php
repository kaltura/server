<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaGoogleVideoSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaGoogleVideoSyndicationFeedBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaGoogleVideoSyndicationFeedBaseFilter::$order_by_map);
	}
}
