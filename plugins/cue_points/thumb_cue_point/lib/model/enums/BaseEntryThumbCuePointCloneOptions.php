<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class BaseEntryThumbCuePointCloneOptions implements IKalturaPluginEnum, BaseEntryCloneOptions
{
	const THUMB_CUE_POINTS = "THUMB_CUE_POINTS";

	public static function getAdditionalValues()
	{
		return array(
			"THUMB_CUE_POINTS" => self::THUMB_CUE_POINTS,
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