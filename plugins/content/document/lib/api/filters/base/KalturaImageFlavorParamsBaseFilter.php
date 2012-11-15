<?php
/**
 * @package plugins.document
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaImageFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaImageFlavorParamsBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaImageFlavorParamsBaseFilter::$order_by_map);
	}
}
