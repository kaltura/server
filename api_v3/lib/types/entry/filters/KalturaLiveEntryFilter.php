<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveEntryFilter extends KalturaLiveEntryBaseFilter
{
	static private $map_between_objects = array
	(
		"isLive" => "_is_live",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isLive;
}
