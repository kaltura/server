<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizReportType implements IKalturaPluginEnum, ReportType
{
	const QUIZ = 'QUIZ';
	const QUIZ_USER_PERCENTAGE = 'QUIZ_USER_PERCENTAGE';
	const QUIZ_AGGREGATE_BY_QUESTION = 'QUIZ_AGGREGATE_BY_QUESTION';
	const QUIZ_USER_AGGREGATE_BY_QUESTION = 'QUIZ_USER_AGGREGATE_BY_QUESTION';

	public static function getAdditionalValues()
	{
		return array(
			'QUIZ' => self::QUIZ,
			'QUIZ_USER_PERCENTAGE' => self::QUIZ_USER_PERCENTAGE,
			'QUIZ_AGGREGATE_BY_QUESTION' => self::QUIZ_AGGREGATE_BY_QUESTION,
			'QUIZ_USER_AGGREGATE_BY_QUESTION' => self::QUIZ_USER_AGGREGATE_BY_QUESTION,
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