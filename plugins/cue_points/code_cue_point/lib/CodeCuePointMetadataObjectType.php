<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage lib.enum
 */
class CodeCuePointMetadataObjectType implements IKalturaPluginEnum, MetadataObjectType
{
	const CODE_CUE_POINT = 'CodeCuePoint';
	
	public static function getAdditionalValues()
	{
		return array(
			'CODE_CUE_POINT' => self::CODE_CUE_POINT,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
