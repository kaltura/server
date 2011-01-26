<?php
/**
 * @package api
 * @subpackage enum
 */
class VerizonDistributionProviderType extends KalturaDistributionProviderType
{
	const VERIZON = 'VERIZON';
	
	/**
	 * @var VerizonDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return VerizonDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new VerizonDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'VERIZON' => self::VERIZON,
		);
	}
	
	public function getPluginName()
	{
		return VerizonDistributionPlugin::getPluginName();
	}
}
