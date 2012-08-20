<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.enum
 */
class KalturaDropFolderFileHandlerType extends KalturaDynamicEnum implements DropFolderFileHandlerType
{
	public static function getEnumClass()
	{
		return 'DropFolderFileHandlerType';
	}
}