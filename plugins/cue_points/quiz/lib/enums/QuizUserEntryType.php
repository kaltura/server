<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const QUIZ = 'QUIZ';

	public static function getAdditionalValues()
	{
		return array(
			'QUIZ' => self::QUIZ
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