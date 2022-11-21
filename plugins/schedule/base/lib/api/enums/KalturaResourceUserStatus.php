<?php

/**
 * @package plugins.schedule
 * @subpackage api.enum
 */
class KalturaResourceUserStatus extends KalturaDynamicEnum implements ResourceUserStatus
{

	/**
	 * @inheritDoc
	 */
	public static function getEnumClass()
	{
		return 'ResourceUserStatus';
	}
}