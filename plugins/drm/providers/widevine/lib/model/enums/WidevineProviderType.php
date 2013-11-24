<?php
/**
 * @package plugins.widevine
 * @subpackage model.enum
 */
class WidevineProviderType implements IKalturaPluginEnum, DrmProviderType
{
	const WIDEVINE = 'WIDEVINE';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'WIDEVINE' => self::WIDEVINE,
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