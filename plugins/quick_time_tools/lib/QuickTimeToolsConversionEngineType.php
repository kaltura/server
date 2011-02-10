<?php
/**
 * @package plugins.quickTimeTools
 * @subpackage lib
 */
class QuickTimeToolsConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const QUICK_TIME_PLAYER_TOOLS = 'QuickTimeTools';
	
	public static function getAdditionalValues()
	{
		return array(
			'QUICK_TIME_PLAYER_TOOLS' => self::QUICK_TIME_PLAYER_TOOLS
		);
	}
}
