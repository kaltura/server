<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaLiveChannelSegmentStatus extends KalturaDynamicEnum implements LiveChannelSegmentStatus
{
	public static function getEnumClass()
	{
		return 'LiveChannelSegmentStatus';
	}
}