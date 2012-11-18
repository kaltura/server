<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaAmazonS3StorageProfileBaseFilter extends KalturaStorageProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaAmazonS3StorageProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaAmazonS3StorageProfileBaseFilter::$order_by_map);
	}
}
