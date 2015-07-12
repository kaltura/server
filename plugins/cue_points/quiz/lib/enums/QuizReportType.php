<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizReportType implements IKalturaPluginEnum, ReportType
{
	const QUIZ = 'QUIZ';
	const QUIZ_USER_PERCENTAGE = 'self::QUIZ_USER_PERCENTAGE';

	public static function getAdditionalValues()
	{
		return array(
			'QUIZ' => self::QUIZ,
			'QUIZ_USER_PERCENTAGE' => self::QUIZ_USER_PERCENTAGE
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