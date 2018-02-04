<?php
/**
 * @package plugins.konference
 * @subpackage model.enum
 */
class ConferenceEntryType implements IKalturaPluginEnum, entryType
{
	const CONFERENCE = 'conference';
	
	public static function getAdditionalValues()
	{
		return array(
			'CONFERENCE' => self::CONFERENCE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ExternalMediaPlugin::getApiValue(self::CONFERENCE) => 'Conference',
		);
	}
}
