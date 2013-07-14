<?php
/**
 * @package plugins.captions
 * @subpackage model.enum
 */
class CaptionObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
	const CAPTIONS = 'Captions';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'CAPTIONS' => self::CAPTIONS,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}