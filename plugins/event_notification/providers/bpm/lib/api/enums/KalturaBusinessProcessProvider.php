<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.enum
 * @see BusinessProcessProvider
 */
class KalturaBusinessProcessProvider extends KalturaDynamicEnum implements BusinessProcessProvider
{
	public static function getEnumClass()
	{
		return 'BusinessProcessProvider';
	}
}