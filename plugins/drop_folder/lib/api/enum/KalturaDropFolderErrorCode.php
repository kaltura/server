<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.enum
 */
class KalturaDropFolderErrorCode extends KalturaDynamicEnum implements DropFolderErrorCode
{
	// see DropFolderErrorCode interface
	
	public static function getEnumClass()
	{
		return 'DropFolderErrorCode';
	}
}
