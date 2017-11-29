<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaEntryFilterForPlaylist extends KalturaMediaEntryFilter
{
	static private $map_between_objects = array
	(
		"limit" => "_limit",
		"name" => "_name",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * 
	 * 
	 * @var int
	 */
	public $limit;

	/**
	 *
	 *
	 * @var string
	 */
	public $name;
}
