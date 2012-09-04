<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaMediaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaMediaFlavorParamsOutputBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaMediaFlavorParamsOutputBaseFilter::$order_by_map);
	}
}
