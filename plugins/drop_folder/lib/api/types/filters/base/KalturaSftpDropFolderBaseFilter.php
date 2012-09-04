<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSftpDropFolderBaseFilter extends KalturaSshDropFolderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaSftpDropFolderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaSftpDropFolderBaseFilter::$order_by_map);
	}
}
