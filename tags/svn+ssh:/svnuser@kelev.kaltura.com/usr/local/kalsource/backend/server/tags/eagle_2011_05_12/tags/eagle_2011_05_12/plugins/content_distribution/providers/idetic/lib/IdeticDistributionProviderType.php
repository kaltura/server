<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage lib
 */
class IdeticDistributionProviderType extends KalturaDistributionProviderType
{
	const IDETIC = 'IDETIC';
	
	/**
	 * @var IdeticDistributionProviderType
	 */
	protected static $instance;

	/**
	 * @return IdeticDistributionProviderType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new IdeticDistributionProviderType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'IDETIC' => self::IDETIC,
		);
	}
	
	public function getPluginName()
	{
		return IdeticDistributionPlugin::getPluginName();
	}
}
