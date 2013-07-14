<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage lib
 */
class ComcastMrssDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const COMCAST_MRSS = 'COMCAST_MRSS';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ComcastMrssDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'COMCAST_MRSS' => self::COMCAST_MRSS,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
	
	public function getPluginName()
	{
		return ComcastMrssDistributionPlugin::getPluginName();
	}	
}
