<?php
/**
 * @package plugins.ApFeedDropFolder
 * @subpackage model.enum
 */
class ApFeedDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const AP_FEED = 'AP_FEED';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array('AP_FEED' => self::AP_FEED);
		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}