<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaLiveChannelSegmentType extends KalturaDynamicEnum implements LiveChannelSegmentType
{
	public static function getEnumClass()
	{
		return 'LiveChannelSegmentType';
	}
}