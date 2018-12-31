<?php
/**
 * @package plugins.videoIndexerDistribution
 * @subpackage lib
 */
class VideoIndexerDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const VIDEOINDEXER = 'VIDEOINDEXER';
	
	public static function getAdditionalValues()
	{
		return array(
			'VIDEOINDEXER' => self::VIDEOINDEXER,
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
