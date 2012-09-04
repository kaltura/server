<?php
/**
 * @package plugins.document
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaPdfFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaPdfFlavorParamsBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaPdfFlavorParamsBaseFilter::$order_by_map);
	}
}
