<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage lib
 */
class UverseClickToOrderDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const UVERSE_CLICK_TO_ORDER = 'UVERSE_CLICK_TO_ORDER';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new UverseClickToOrderDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'UVERSE_CLICK_TO_ORDER' => self::UVERSE_CLICK_TO_ORDER,
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
		return UverseClickToOrderDistributionPlugin::getPluginName();
	}	
}
