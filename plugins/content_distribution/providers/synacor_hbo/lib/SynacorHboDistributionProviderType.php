<?php
/**
 * @package plugins.synacorHboDistribution
 * @subpackage lib
 */
class SynacorHboDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const SYNACOR_HBO = 'SYNACOR_HBO';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new SynacorHboDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'SYNACOR_HBO' => self::SYNACOR_HBO,
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
		return SynacorHboDistributionPlugin::getPluginName();
	}	
}
