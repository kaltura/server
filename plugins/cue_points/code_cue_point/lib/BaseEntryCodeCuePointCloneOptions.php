<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class BaseEntryCodeCuePointCloneOptions implements IKalturaPluginEnum, BaseEntryCloneOptions
{
	const CODE_CUE_POINTS = "CODE_CUE_POINTS";

	public static function getAdditionalValues()
	{
		return array(
			"CODE_CUE_POINTS" => self::CODE_CUE_POINTS,
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