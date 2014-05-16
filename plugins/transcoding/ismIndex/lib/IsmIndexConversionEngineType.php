<?php
/**
 * @package plugins.ismIndex
 * @subpackage lib
 */
class IsmIndexConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const ISMINDEX = 'IsmIndex';
	const ISM_MANIFEST = 'IsmManifest';
	
	public static function getAdditionalValues()
	{
		return array(
			'ISMINDEX' => self::ISMINDEX,
			'ISM_MANIFEST' => self::ISM_MANIFEST
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
