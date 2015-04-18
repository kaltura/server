<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model.enum
 */
class FeedDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const FEED = 'FEED';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array('FEED' => self::FEED);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}