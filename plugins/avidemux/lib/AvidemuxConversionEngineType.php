<?php
/**
 * @package api
 * @subpackage enum
 */
class AvidemuxConversionEngineType extends KalturaConversionEngineType
{
	const AVIDEMUX = 'Avidemux';
	
	/**
	 * @var AvidemuxConversionEngineType
	 */
	protected static $instance;

	/**
	 * @return FastStartConversionEngineType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new AvidemuxConversionEngineType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'AVIDEMUX' => self::AVIDEMUX
		);
	}
	
	public function getPluginName()
	{
		return AvidemuxPlugin::getPluginName();
	}
}
