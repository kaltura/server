<?php
/**
 * @package plugins.fairplay
 * @subpackage model.enum
 */
class FairplayProviderType implements IKalturaPluginEnum, DrmProviderType
{
	const FAIRPLAY = 'FAIRPLAY';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'FAIRPLAY' => self::FAIRPLAY,
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