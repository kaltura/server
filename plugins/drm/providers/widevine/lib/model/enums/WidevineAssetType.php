<?php
/**
 * @package plugins.widevine
 * @subpackage model.enum
 */
class WidevineAssetType implements IKalturaPluginEnum, assetType
{
	const WIDEVINE_FLAVOR = 'WidevineFlavor';
	
	public static function getAdditionalValues()
	{
		return array(
			'WIDEVINE_FLAVOR' => self::WIDEVINE_FLAVOR,
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
