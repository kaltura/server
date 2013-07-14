<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage lib
 */
class TimeWarnerDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const TIME_WARNER = 'TIME_WARNER';
	
	/**
	 * @return SyndicationDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new TimeWarnerDistributionProviderType();
			
		return self::$instance;
	}
		
	public static function getAdditionalValues()
	{
		return array(
			'TIME_WARNER' => self::TIME_WARNER,
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
		return TimeWarnerDistributionPlugin::getPluginName();
	}	
}
