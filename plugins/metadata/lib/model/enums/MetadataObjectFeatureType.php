<?php
/**
 * @package plugins.metadata
 *  @subpackage model.enum
 */
class MetadataObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
	const CUSTOM_DATA = 'CustomData';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'CUSTOM_DATA' => self::CUSTOM_DATA,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}