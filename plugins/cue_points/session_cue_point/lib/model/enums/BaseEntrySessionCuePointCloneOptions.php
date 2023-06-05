<?php
/**
 * @package plugins.sessionCuePoint
 * @subpackage model.enum
 */
class BaseEntrySessionCuePointCloneOptions implements IKalturaPluginEnum, BaseEntryCloneOptions
{
	const SESSION_CUE_POINTS = "SESSION_CUE_POINTS";

	public static function getAdditionalValues()
	{
		return array(
			"SESSION_CUE_POINTS" => self::SESSION_CUE_POINTS,
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
