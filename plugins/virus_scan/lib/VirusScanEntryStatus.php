<?php
/**
 * @package api
 * @subpackage enum
 */
class VirusScanEntryStatus extends KalturaEntryStatus
{
	const INFECTED = 'Infected';
	
	/**
	 * @var VirusScanEntryStatus
	 */
	protected static $instance;

	/**
	 * @return VirusScanEntryStatus
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new VirusScanEntryStatus();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'INFECTED' => self::INFECTED
		);
	}
	
	public function getPluginName()
	{
		return VirusScanPlugin::getPluginName();
	}
}
