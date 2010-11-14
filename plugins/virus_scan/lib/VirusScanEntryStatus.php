<?php
/**
 * @package api
 * @subpackage enum
 */
class VirusScanEntryStatus extends KalturaPluginEnum implements entryStatus
{
	const INFECTED = 'Infected';
	
	/**
	 * @var VirusScanEntryStatus
	 */
	protected static $instance;

	private function __construct(){}
	
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
	
	public function getEnumClass()
	{
		return 'entryStatus';
	}
	
	public function getPluginName()
	{
		return VirusScanPlugin::getPluginName();
	}
}
