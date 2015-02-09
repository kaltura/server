<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage lib.enum
 */
class timedThumbAssetType implements IKalturaPluginEnum, assetType
{
	const TIMED_THUMB_ASSET = 'timedThumb';
	
	public static function getAdditionalValues()
	{
		return array(
			'TIMED_THUMB_ASSET' => self::TIMED_THUMB_ASSET,
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