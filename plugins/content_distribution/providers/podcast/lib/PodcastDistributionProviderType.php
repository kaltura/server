<?php
/**
 * @package plugins.podcastDistribution
 * @subpackage lib
 */
class PodcastDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const PODCAST = 'PODCAST';
	
	public static function getAdditionalValues()
	{
		return array(
			'PODCAST' => self::PODCAST,
		);
	}
}
