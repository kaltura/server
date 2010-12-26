<?php
/**
 * @package api
 * @subpackage enum
 */
class YouTubeDistributionProviderType extends KalturaDistributionProviderType
{
	const YOUTUBE = 'YOUTUBE';
	
	/**
	 * @var YouTubeDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return YouTubeDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new YouTubeDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'YOUTUBE' => self::YOUTUBE,
		);
	}
	
	public function getPluginName()
	{
		return YouTubeDistributionPlugin::getPluginName();
	}
}
