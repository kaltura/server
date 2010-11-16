<?php
/**
 * @package api
 * @subpackage enum
 */
class QuickTimeToolsConversionEngineType extends KalturaConversionEngineType
{
	const QUICK_TIME_PLAYER_TOOLS = 'QuickTimeTools';
	
	/**
	 * @var QuickTimeToolsConversionEngineType
	 */
	protected static $instance;

	/**
	 * @return QuickTimeToolsConversionEngineType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new QuickTimeToolsConversionEngineType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'QUICK_TIME_PLAYER_TOOLS' => self::QUICK_TIME_PLAYER_TOOLS
		);
	}
	
	public function getPluginName()
	{
		return QuickTimeToolsPlugin::getPluginName();
	}
}
