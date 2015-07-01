<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizReportType implements IKalturaPluginEnum, ReportType
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