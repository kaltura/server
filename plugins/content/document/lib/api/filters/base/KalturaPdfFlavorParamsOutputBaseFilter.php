<?php
/**
 * @package plugins.document
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaPdfFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaPdfFlavorParamsOutputBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaPdfFlavorParamsOutputBaseFilter::$order_by_map);
	}
}
