<?php
/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizCuePointType implements IKalturaPluginEnum, CuePointType
{
	const QUIZ_QUESTION = 'QUIZ_QUESTION';
	const QUIZ_ANSWER = 'QUIZ_ANSWER';

	public static function getAdditionalValues()
	{
		return array(
			'QUIZ_QUESTION' => self::QUIZ_QUESTION,
			'QUIZ_ANSWER' => self::QUIZ_ANSWER,
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
