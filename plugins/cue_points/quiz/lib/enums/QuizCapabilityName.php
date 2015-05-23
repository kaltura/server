<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizCapabilityName implements IKalturaPluginEnum, CapabilityName
{

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array(
			'KALTURA_QUIZ_CAPABILITY_NAME' => QuizPlugin::PLUGIN_NAME
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