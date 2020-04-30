<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSharedFileSystemType extends KalturaDynamicEnum implements kSharedFileSystemMgrType
{
	public static function getEnumClass()
	{
		return 'kSharedFileSystemMgrType';
	}
}
