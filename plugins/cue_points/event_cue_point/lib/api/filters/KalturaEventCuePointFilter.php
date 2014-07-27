<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage api.filters
 */
class KalturaEventCuePointFilter extends KalturaEventCuePointBaseFilter
{
	static private $map_between_objects = array
	(
			"eventTypeEqual" => "_eq_sub_type",
			"eventTypeIn" => "_in_sub_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
