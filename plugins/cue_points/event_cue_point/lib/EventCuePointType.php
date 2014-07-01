<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage lib.enum
 */
class EventCuePointType implements IKalturaPluginEnum, CuePointType
{
	const EVENT = 'Event';
	
	public static function getAdditionalValues()
	{
		return array(
			'EVENT' => self::EVENT,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
