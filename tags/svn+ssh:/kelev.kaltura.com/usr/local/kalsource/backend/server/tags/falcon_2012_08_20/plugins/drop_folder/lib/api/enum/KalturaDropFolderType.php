<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.enum
 */
class KalturaDropFolderType extends KalturaDynamicEnum implements DropFolderType
{
	public static function getEnumClass()
	{
		return 'DropFolderType';
	}
}