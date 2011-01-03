<?php
/**
 * @package api
 * @subpackage enum
 */
class ComcastDistributionProviderType extends KalturaDistributionProviderType
{
	const COMCAST = 'COMCAST';
	
	/**
	 * @var ComcastDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return ComcastDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ComcastDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'COMCAST' => self::COMCAST,
		);
	}
	
	public function getPluginName()
	{
		return ComcastDistributionPlugin::getPluginName();
	}
}
