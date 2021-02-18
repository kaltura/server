<?php

/**
 * @package plugins.vendor.Zoom.ZoomDropFolder
 * @subpackage model.enum
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