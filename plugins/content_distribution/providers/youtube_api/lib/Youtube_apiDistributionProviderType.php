<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage lib
 */
class Youtube_apiDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const YOUTUBE_API = 'YOUTUBE_API';
	
	public static function getAdditionalValues()
	{
		return array(
			'YOUTUBE_API' => self::YOUTUBE_API,
		);
	}
}
