<?php
/**
 * @package plugins.kontiki
 *  @subpackage model.enum
 */
class KontikiStorageProfileProtocol implements IKalturaPluginEnum, StorageProfileProtocol
{
	const KONTIKI = 'KONTIKI';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array('KONTIKI' => self::KONTIKI);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}

	
}