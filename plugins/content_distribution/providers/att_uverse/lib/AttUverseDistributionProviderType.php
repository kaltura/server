<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage lib
 */
class AttUverseDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const ATT_UVERSE = 'ATT_UVERSE';
	
	public static function getAdditionalValues()
	{
		return array(
			'ATT_UVERSE' => self::ATT_UVERSE,
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
