<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage lib
 */
class IdeticDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
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
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
	
	public function getPluginName()
	{
		return IdeticDistributionPlugin::getPluginName();
	}
}
