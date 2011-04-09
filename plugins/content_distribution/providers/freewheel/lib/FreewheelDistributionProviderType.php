<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage lib
 */
class FreewheelDistributionProviderType extends KalturaDistributionProviderType
{
	const FREEWHEEL = 'FREEWHEEL';
	
	/**
	 * @var FreewheelDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return FreewheelDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new FreewheelDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'FREEWHEEL' => self::FREEWHEEL,
		);
	}
	
	public function getPluginName()
	{
		return FreewheelDistributionPlugin::getPluginName();
	}
}
