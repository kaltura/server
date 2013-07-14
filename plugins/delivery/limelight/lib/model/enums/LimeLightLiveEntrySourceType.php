<?php

/**
 * @package plugins.limeLight
 * @subpackage model.enum
 */ 
class LimeLightLiveEntrySourceType implements IKalturaPluginEnum, EntrySourceType
{
	const LIMELIGHT_LIVE = 'LIVE_STREAM';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'LIMELIGHT_LIVE' => self::LIMELIGHT_LIVE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
