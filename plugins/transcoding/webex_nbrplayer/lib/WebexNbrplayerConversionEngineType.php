<?php
/**
 * @package plugins.webexNbrplayer
 * @subpackage lib
 */
class WebexNbrplayerConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const WEBEX_NBRPLAYER = 'WebexNbrplayer';
	
	public static function getAdditionalValues()
	{
		return array(
			'WEBEX_NBRPLAYER' => self::WEBEX_NBRPLAYER
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
