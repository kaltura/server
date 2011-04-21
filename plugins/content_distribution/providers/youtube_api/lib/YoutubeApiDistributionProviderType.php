<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage lib
 */
class YoutubeApiDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const YOUTUBE_API = 'YOUTUBE_API';
	
	public static function getAdditionalValues()
	{
		return array(
			'YOUTUBE_API' => self::YOUTUBE_API,
		);
	}
}
