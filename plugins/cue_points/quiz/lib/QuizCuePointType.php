<?php
/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizCuePointType implements IKalturaPluginEnum, CuePointType
{
	const QUESTION = 'Question';
	const ANSWER = 'Answer';

	public static function getAdditionalValues()
	{
		return array(
			'QUESTION' => self::QUESTION,
			'ANSWER' => self::ANSWER,
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
