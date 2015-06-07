<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizUserEntryStatus implements IKalturaPluginEnum {

	const USER_ENTRY_STATUS_SUBMITTED = 3;

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array
		(
			'USER_ENTRY_STATUS_SUBMITTED' => self::USER_ENTRY_STATUS_SUBMITTED,
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