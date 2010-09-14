<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaEntryFilterForPlaylist extends KalturaMediaEntryFilter
{
	private $map_between_objects = array
	(
		"limit" => "_limit",
	);

	private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var int
	 */
	public $limit;
}
