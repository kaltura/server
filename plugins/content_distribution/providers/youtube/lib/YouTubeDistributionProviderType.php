<?php
/**
 * @package api
 * @subpackage enum
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
