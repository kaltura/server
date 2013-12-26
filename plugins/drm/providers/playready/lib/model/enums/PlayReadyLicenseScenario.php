<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */
class PlayReadyLicenseScenario implements IKalturaPluginEnum, DrmLicenseScenario
{
	const PROTECTION = 'PROTECTION';
	const PURCHASE = 'PURCHASE';
	const RENTAL = 'RENTAL';
	const SUBSCRIPTION = 'SUBSCRIPTION';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'PROTECTION' => self::PROTECTION,
			'PURCHASE' => self::PURCHASE,
			'RENTAL' => self::RENTAL,
			'SUBSCRIPTION' => self::SUBSCRIPTION,
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