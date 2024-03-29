<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaLiveStreamScheduleEventFilter extends KalturaLiveStreamScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::LIVE_STREAM;
	}

	static private $map_between_objects = array
	(
		"sourceEntryIdEqual" => "_eq_source_entry_id"
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
	 * @var string
	 */
	public $sourceEntryIdEqual;
}
