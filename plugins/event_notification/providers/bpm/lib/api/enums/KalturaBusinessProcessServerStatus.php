<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.enum
 */
class KalturaBusinessProcessServerStatus extends KalturaDynamicEnum implements BusinessProcessServerStatus
{
	public static function getEnumClass()
	{
		return 'BusinessProcessServerStatus';
	}
}