<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage lib
 */
class PushToNewsDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const PUSH_TO_NEWS = 'PUSH_TO_NEWS';
	
	public static function getAdditionalValues()
	{
		return array(
			'PUSH_TO_NEWS' => self::PUSH_TO_NEWS,
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
