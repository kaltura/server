<?php
/**
 * @package api
 * @subpackage enum
 */
class MsnDistributionProviderType extends KalturaDistributionProviderType
{
	const MSN = 'MSN';
	
	/**
	 * @var MsnDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return MsnDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new MsnDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'MSN' => self::MSN,
		);
	}
	
	public function getPluginName()
	{
		return DistributionMsnPlugin::getPluginName();
	}
}
