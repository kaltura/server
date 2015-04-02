<?php
/**
 * @package plugins.dropFolderMRSS
 *  @subpackage model.enum
 */
class MRSSDropFolderType implements IKalturaPluginEnum, DropFolderType
{
	const MRSS = 'MRSS';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array('MRSS' => self::MRSS);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}