<?php
/**
 * @package api
 * @subpackage enum
 */
class ContentDistributionFileSyncObjectType extends KalturaFileSyncObjectType
{
	const GENERIC_DISTRIBUTION_ACTION = 'GenericDistributionAction';
	
	/**
	 * @var ContentDistributionFileSyncObjectType
	 */
	protected static $instance;

	/**
	 * @return ContentDistributionFileSyncObjectType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ContentDistributionFileSyncObjectType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'GENERIC_DISTRIBUTION_ACTION' => self::GENERIC_DISTRIBUTION_ACTION,
		);
	}
	
	public function getPluginName()
	{
		return ContentDistributionPlugin::getPluginName();
	}
}
