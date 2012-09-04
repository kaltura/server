<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSshDropFolderBaseFilter extends KalturaRemoteDropFolderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaSshDropFolderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaSshDropFolderBaseFilter::$order_by_map);
	}
}
