<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class CuePointObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
	const CUE_POINT = 'CuePoint';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'CUE_POINT' => self::CUE_POINT,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}