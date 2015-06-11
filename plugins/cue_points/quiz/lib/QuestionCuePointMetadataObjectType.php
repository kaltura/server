<?php
/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuestionCuePointMetadataObjectType implements IKalturaPluginEnum, MetadataObjectType
{
	const QUESTION_CUE_POINT = 'QuestionCuePoint';
	
	public static function getAdditionalValues()
	{
		return array(
			'QUESTION_CUE_POINT' => self::QUESTION_CUE_POINT,
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
