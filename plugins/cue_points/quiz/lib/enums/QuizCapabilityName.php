<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizEntryCapability implements IKalturaPluginEnum, EntryCapability
{

	const QUIZ_CAPABILITY_NAME = 'quiz';

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array(
			'KALTURA_QUIZ_CAPABILITY_NAME' => self::QUIZ_CAPABILITY_NAME
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