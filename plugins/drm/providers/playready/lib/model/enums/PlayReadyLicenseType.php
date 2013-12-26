<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */
class PlayReadyLicenseType implements IKalturaPluginEnum, DrmLicenseType
{
	const PERSISTENT = 'PERSISTENT';
	const NON_PERSISTENT = 'NON_PERSISTENT';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'PERSISTENT' => self::PERSISTENT,
			'NON_PERSISTENT' => self::NON_PERSISTENT,
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