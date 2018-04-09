<?php
/**
 * @package plugins.conference
 * @subpackage lib.enum
 */
class ConferenceServerNodeType implements IKalturaPluginEnum, serverNodeType
{
	const CONFERENCE_SERVER = 'CONFERENCE_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'CONFERENCE_SERVER' => self::CONFERENCE_SERVER,
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