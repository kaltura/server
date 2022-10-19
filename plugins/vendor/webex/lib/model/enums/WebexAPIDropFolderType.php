<?php

/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage lib.enums
 */
class WebexAPIDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const WEBEX_API = 'WEBEX_API';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array('WEBEX_API' => self::WEBEX_API);
		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}