<?php

/**
 * @package plugins.ask
 * @subpackage lib.enum
 */
class AskReportType implements IKalturaPluginEnum, ReportType
{
	const ASK = 'ASK';
	const ASK_USER_PERCENTAGE = 'ASK_USER_PERCENTAGE';
	const ASK_AGGREGATE_BY_QUESTION = 'ASK_AGGREGATE_BY_QUESTION';
	const ASK_USER_AGGREGATE_BY_QUESTION = 'ASK_USER_AGGREGATE_BY_QUESTION';

	public static function getAdditionalValues()
	{
		return array(
			'ASK' => self::ASK,
			'ASK_USER_PERCENTAGE' => self::ASK_USER_PERCENTAGE,
			'ASK_AGGREGATE_BY_QUESTION' => self::ASK_AGGREGATE_BY_QUESTION,
			'ASK_USER_AGGREGATE_BY_QUESTION' => self::ASK_USER_AGGREGATE_BY_QUESTION,
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