<?php

/**
 * @package plugins.velocix
 * @subpackage model.enum
 */ 
class VelocixLiveEntrySourceType implements IKalturaPluginEnum, EntrySourceType
{
	const VELOCIX_LIVE = 'VELOCIX_LIVE';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'VELOCIX_LIVE' => self::VELOCIX_LIVE,
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
