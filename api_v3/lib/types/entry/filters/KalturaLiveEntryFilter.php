<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveEntryFilter extends KalturaLiveEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::LIVE_CHANNEL . ',' . KalturaEntryType::LIVE_STREAM;
	}
	
	static private $map_between_objects = array
	(
		"isLive" => "_is_live",
		"isRecordedEntryIdEmpty" => "_is_recorded_entry_id_empty",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isLive;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isRecordedEntryIdEmpty;
}
