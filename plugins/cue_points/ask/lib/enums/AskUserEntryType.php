<?php

/**
 * @package plugins.ask
 * @subpackage lib.enum
 */
class AskUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const ASK = 'ASK';

	public static function getAdditionalValues()
	{
		return array(
			'ASK' => self::ASK
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