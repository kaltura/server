<?php
/**
 * @package api
 * @subpackage enum
 */
class HuluDistributionProviderType extends KalturaDistributionProviderType
{
	const HULU = 'HULU';
	
	/**
	 * @var HuluDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return HuluDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new HuluDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'HULU' => self::HULU,
		);
	}
	
	public function getPluginName()
	{
		return HuluDistributionPlugin::getPluginName();
	}
}
