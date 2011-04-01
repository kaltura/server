<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaDropFolderType extends KalturaDynamicEnum implements DropFolderType
{
	public static function getEnumClass()
	{
		return 'DropFolderType';
	}
}