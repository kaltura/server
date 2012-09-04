<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaGenericXsltSyndicationFeedBaseFilter extends KalturaGenericSyndicationFeedFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaGenericXsltSyndicationFeedBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaGenericXsltSyndicationFeedBaseFilter::$order_by_map);
	}
}
