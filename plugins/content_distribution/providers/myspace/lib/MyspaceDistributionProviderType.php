<?php
/**
 * @package api
 * @subpackage enum
 */
class MyspaceDistributionProviderType extends KalturaDistributionProviderType
{
	const MYSPACE = 'MYSPACE';
	
	/**
	 * @var MyspaceDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return MyspaceDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new MyspaceDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'MYSPACE' => self::MYSPACE,
		);
	}
	
	public function getPluginName()
	{
		return MyspaceDistributionPlugin::getPluginName();
	}
}
