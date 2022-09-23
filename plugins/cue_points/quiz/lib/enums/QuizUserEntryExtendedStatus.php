<?php

/**
 * @package plugins.quiz
 * @subpackage lib.enum
 */
class QuizUserEntryExtendedStatus implements IKalturaPluginEnum, UserEntryExtendedStatus
{
	const SYNC_STATUS_SUCCESS = 'SYNC_STATUS_SUCCESS';
	const SYNC_STATUS_ERROR = 'SYNC_STATUS_ERROR';

	public static function getAdditionalValues()
	{
		return array(
			'SYNC_STATUS_SUCCESS' => self::SYNC_STATUS_SUCCESS,
			'SYNC_STATUS_ERROR' => self::SYNC_STATUS_ERROR,
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