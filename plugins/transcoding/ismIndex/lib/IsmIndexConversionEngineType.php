<?php
/**
 * @package plugins.ismIndex
 * @subpackage lib
 */
class IsmIndexConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const ISMINDEX = 'IsmIndex';
	
	public static function getAdditionalValues()
	{
		return array(
			'ISMINDEX' => self::ISMINDEX
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
