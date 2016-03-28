<?php

/**
 * @package plugins.ask
 * @subpackage lib.enum
 */
class AskEntryCapability implements IKalturaPluginEnum, EntryCapability
{

	const ASK = 'ask';

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array(
			'KALTURA_ASK_CAPABILITY_NAME' => self::ASK
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