<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizUserEntryStatus implements IKalturaPluginEnum,UserEntryStatus {

	const QUIZ_SUBMITTED = 3;

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array
		(
			'QUIZ_SUBMITTED' => self::QUIZ_SUBMITTED,
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