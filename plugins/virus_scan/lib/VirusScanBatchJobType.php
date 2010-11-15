<?php
/**
 * @package api
 * @subpackage enum
 */
class VirusScanBatchJobType extends KalturaPluginEnum implements BatchJobType
{
	const VIRUS_SCAN = 'VirusScan';
	
	/**
	 * @var VirusScanBatchJobType
	 */
	protected static $instance;

	private function __construct(){}
	
	/**
	 * @return VirusScanBatchJobType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new VirusScanBatchJobType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'VIRUS_SCAN' => self::VIRUS_SCAN
		);
	}
	
	public function getEnumClass()
	{
		return 'BatchJobType';
	}
	
	public function getPluginName()
	{
		return VirusScanPlugin::getPluginName();
	}
}
