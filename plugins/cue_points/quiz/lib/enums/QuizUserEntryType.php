<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const KALTURA_QUIZ_USER_ENTRY = 1;

	public static function getAdditionalValues()
	{
		return array(
			'KALTURA_QUIZ_USER_ENTRY' => self::KALTURA_QUIZ_USER_ENTRY
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