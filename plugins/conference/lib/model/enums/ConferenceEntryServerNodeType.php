<?php
/**
 * @package plugins.conference
 * @subpackage lib.enum
 */
class ConferenceEntryServerNodeType implements IKalturaPluginEnum, EntryServerNodeType
{
	const CONFERENCE_ENTRY_SERVER = 'CONFERENCE_ENTRY_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'CONFERENCE_ENTRY_SERVER' => self::CONFERENCE_ENTRY_SERVER,
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