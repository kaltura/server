<?php
/**
 * @package plugins.fairplay
 * @subpackage model.enum
 */
class FairplayPermissionName implements IKalturaPluginEnum, PermissionName
{
	const FEATURE_FAIRPLAY_OFFLINE_PLAY = 'FEATURE_FAIRPLAY_OFFLINE_PLAY';

	public static function getAdditionalValues()
	{
		return array
		(
			'FEATURE_FAIRPLAY_OFFLINE_PLAY' => self::FEATURE_FAIRPLAY_OFFLINE_PLAY,
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