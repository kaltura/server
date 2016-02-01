<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class BaseEntryAnnotationCuePointCloneOptions implements IKalturaPluginEnum, BaseEntryCloneOptions
{
	const ANNOTATION_CUE_POINTS = "ANNOTATION_CUE_POINTS";

	public static function getAdditionalValues()
	{
		return array(
			"ANNOTATION_CUE_POINTS" => self::ANNOTATION_CUE_POINTS,
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