<?php

/**
 * @package plugins.ask
 * @subpackage lib.enum
 */
class AskUserEntryStatus implements IKalturaPluginEnum,UserEntryStatus {

	const ASK_SUBMITTED = 3;

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array
		(
			'ASK_SUBMITTED' => self::ASK_SUBMITTED,
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