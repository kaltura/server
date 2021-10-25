<?php
/**
 * @package plugins.virtualEvent
 * @relatedService ScheduleEventService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVirtualScheduleEventBaseFilter extends KalturaScheduleEventFilter
{
	static private $map_between_objects = array
	(
		"virtualEventIdEqual" => "_eq_virtual_event_id",
		"virtualEventIdIn" => "_in_virtual_event_id",
		"virtualEventIdNotIn" => "_notin_virtual_event_id",
		"virtualScheduleEventSubTypeEqual" => "_eq_virtual_schedule_event_sub_type",
		"virtualScheduleEventSubTypeIn" => "_in_virtual_schedule_event_sub_type",
		"virtualScheduleEventSubTypeNotIn" => "_notin_virtual_schedule_event_sub_type",
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
	 * @var int
	 */
	public $virtualEventIdEqual;

	/**
	 * @var string
	 */
	public $virtualEventIdIn;

	/**
	 * @var string
	 */
	public $virtualEventIdNotIn;

	/**
	 * @var KalturaVirtualScheduleEventSubType
	 */
	public $virtualScheduleEventSubTypeEqual;

	/**
	 * @var string
	 */
	public $virtualScheduleEventSubTypeIn;

	/**
	 * @var string
	 */
	public $virtualScheduleEventSubTypeNotIn;
}
