<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class BaseEntryAdCuePointCloneOptions implements IKalturaPluginEnum, BaseEntryCloneOptions
{
	const AD_CUE_POINTS = "AD_CUE_POINTS";

	public static function getAdditionalValues()
	{
		return array(
			"AD_CUE_POINTS" => self::AD_CUE_POINTS,
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