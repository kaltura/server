<?php
/**
 * @package plugins.visoDistribution
 * @subpackage lib
 */
class VisoDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const VISO = 'VISO';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new VisoDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'VISO' => self::VISO,
		);
	}
	
	public function getPluginName()
	{
		return VisoDistributionPlugin::getPluginName();
	}	
}
