<?php
/**
 * @package plugins.room
 * @relatedService BaseEntryService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaRoomEntryBaseFilter extends KalturaBaseEntryFilter
{
	static private $map_between_objects = array
	(
		"roomTypeEqual" => "_eq_room_type",
		"broadcastEntryIdEqual" => "_eq_broadcast_entry_id",
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
	 * @var KalturaRoomType
	 */
	public $roomTypeEqual;

	/**
	 * @var string
	 */
	public $broadcastEntryIdEqual;
}
