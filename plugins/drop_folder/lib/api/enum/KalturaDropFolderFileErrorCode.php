<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.enum
 */
class KalturaDropFolderFileErrorCode extends KalturaDynamicEnum implements DropFolderFileErrorCode
{
	// see DropFolderFileErrorCode interface
	
	public static function getEnumClass()
	{
		return 'DropFolderFileErrorCode';
	}
}
