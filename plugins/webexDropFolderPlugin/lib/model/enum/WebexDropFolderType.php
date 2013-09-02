<?php
/**
 * @package plugins.webexDropFolder
 *  @subpackage model.enum
 */
class WebexDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const WEBEX = 'WEBEX';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array('WEBEX' => self::WEBEX);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}
