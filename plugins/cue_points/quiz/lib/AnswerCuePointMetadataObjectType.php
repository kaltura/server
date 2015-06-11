<?php
/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class AnswerCuePointMetadataObjectType implements IKalturaPluginEnum, MetadataObjectType
{
	const ANSWER_CUE_POINT = 'AnswerCuePoint';
	
	public static function getAdditionalValues()
	{
		return array(
			'ANSWER_CUE_POINT' => self::ANSWER_CUE_POINT,
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
