<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const YOUTUBE = 'YOUTUBE';
	
	public static function getAdditionalValues()
	{
		return array(
			'YOUTUBE' => self::YOUTUBE,
		);
	}
}
