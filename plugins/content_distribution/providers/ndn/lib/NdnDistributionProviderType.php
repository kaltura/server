<?php
/**
 * @package plugins.ndnDistribution
 * @subpackage lib
 */
class NdnDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const NDN = 'NDN';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new NdnDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'NDN' => self::NDN,
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
		return NdnDistributionPlugin::getPluginName();
	}	
}
