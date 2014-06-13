<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage lib.enum
 */
class ThumbCuePointMetadataObjectType implements IKalturaPluginEnum, MetadataObjectType
{
	const THUMB_CUE_POINT = 'thumbCuePoint';
	
	public static function getAdditionalValues()
	{
		return array(
			'THUMB_CUE_POINT' => self::THUMB_CUE_POINT,
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