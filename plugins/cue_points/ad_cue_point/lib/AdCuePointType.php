<?php
/**
 * @package plugins.adCuePoint
 * @subpackage lib.enum
 */
class AdCuePointType implements IKalturaPluginEnum, CuePointType
{
	const AD = 'Ad';
	
	public static function getAdditionalValues()
	{
		return array(
			'AD' => self::AD,
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
