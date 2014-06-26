<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage api.enum
 */
class KalturaEventType extends KalturaDynamicEnum implements EventType
{
	public static function getEnumClass()
	{
		return 'EventType';
	}
}