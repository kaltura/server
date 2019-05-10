<?php
/**
 * @package plugins.sip
 * @subpackage lib.enum
 */
class SipEntryServerNodeType implements IKalturaPluginEnum, EntryServerNodeType
{
	const SIP_ENTRY_SERVER = 'SIP_ENTRY_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'SIP_ENTRY_SERVER' => self::SIP_ENTRY_SERVER,
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