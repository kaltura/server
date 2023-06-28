<?php
/**
 * @package plugins.sessionCuePoint
 * @subpackage lib.enum
 */
class SessionCuePointType implements IKalturaPluginEnum, CuePointType
{
	const SESSION = 'Session';
	
	public static function getAdditionalValues()
	{
		return array(
			'SESSION' => self::SESSION,
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
