<?php
/**
 * @package api
 * @subpackage enum
 */
class FastStartConversionEngineType extends KalturaConversionEngineType
{
	const FAST_START = 'FastStart';
	
	/**
	 * @var FastStartConversionEngineType
	 */
	protected static $instance;

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
	
	public function getPluginName()
	{
		return FastStartPlugin::getPluginName();
	}
}
