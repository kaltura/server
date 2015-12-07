<?php
/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizCuePointMetadataObjectType implements IKalturaPluginEnum, MetadataObjectType
{
	const ANSWER_CUE_POINT = 'AnswerCuePoint';
	const QUESTION_CUE_POINT = 'QuestionCuePoint';
	
	public static function getAdditionalValues()
	{
		return array(
			'ANSWER_CUE_POINT' => self::ANSWER_CUE_POINT,
			'QUESTION_CUE_POINT' => self::QUESTION_CUE_POINT
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
