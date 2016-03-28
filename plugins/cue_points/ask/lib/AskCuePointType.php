<?php
/**
 * @package plugins.ask
 * @subpackage lib.enum
 */
class AskCuePointType implements IKalturaPluginEnum, CuePointType
{
	const ASK_QUESTION = 'ASK_QUESTION';
	const ASK_ANSWER = 'ASK_ANSWER';

	public static function getAdditionalValues()
	{
		return array(
			'ASK_QUESTION' => self::ASK_QUESTION,
			'ASK_ANSWER' => self::ASK_ANSWER,
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
