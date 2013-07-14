<?php
/**
 * @package plugins.crossKalturaDistribution
 * @subpackage lib
 */
class CrossKalturaDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const CROSS_KALTURA = 'CROSS_KALTURA';
	
	public static function getAdditionalValues()
	{
		return array(
			'CROSS_KALTURA' => self::CROSS_KALTURA,
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
