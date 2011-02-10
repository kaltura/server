<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaFileSyncObjectType extends KalturaDynamicEnum implements FileSyncObjectType 
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'FileSyncObjectType';
	}
}