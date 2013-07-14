<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaModerationFlagStatus extends KalturaDynamicEnum implements moderationFlagStatus
{
	public static function getEnumClass()
	{
		return 'moderationFlagStatus';
	}
}