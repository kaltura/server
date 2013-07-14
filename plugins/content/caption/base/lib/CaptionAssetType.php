<?php
/**
 * @package plugins.caption
 * @subpackage lib.enum
 */
class CaptionAssetType implements IKalturaPluginEnum, assetType
{
	const CAPTION = 'Caption';
	
	public static function getAdditionalValues()
	{
		return array(
			'CAPTION' => self::CAPTION,
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
