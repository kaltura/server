<?php
/**
 * @package plugins.smilManifest
 * @subpackage lib
 */
class SmilManifestConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const SMIL_MANIFEST = 'SmilManifest';
	
	public static function getAdditionalValues()
	{
		return array(
			'SMIL_MANIFEST' => self::SMIL_MANIFEST
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
