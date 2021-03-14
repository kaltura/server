<?php

/**
 * @package plugins.ZoomDropFolder
 * @subpackage lib.enums
 */
class ZoomDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const ZOOM = 'ZOOM';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array('ZOOM' => self::ZOOM);
		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
		
	}
}