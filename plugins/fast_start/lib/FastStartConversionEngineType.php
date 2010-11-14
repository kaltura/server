<?php
/**
 * @package api
 * @subpackage enum
 */
class FastStartConversionEngineType extends KalturaPluginEnum implements conversionEngineType
{
	const FAST_START = 'FastStart';
	
	/**
	 * @var FastStartConversionEngineType
	 */
	protected static $instance;

	private function __construct(){}
	
	/**
	 * @return FastStartConversionEngineType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new FastStartConversionEngineType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'FAST_START' => self::FAST_START
		);
	}
	
	public function getEnumClass()
	{
		return 'conversionEngineType';
	}
	
	public function getPluginName()
	{
		return FastStartPlugin::getPluginName();
	}
}
