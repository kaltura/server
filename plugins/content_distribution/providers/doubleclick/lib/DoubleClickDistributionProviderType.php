<?php
/**
 * @package plugins.doubleClickDistribution
 * @subpackage lib
 */
class DoubleClickDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const DOUBLECLICK = 'DOUBLECLICK';
	
	public static function getAdditionalValues()
	{
		return array(
			'DOUBLECLICK' => self::DOUBLECLICK,
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
