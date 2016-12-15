<?php

/*
* @package plugins.drm
* @subpackage model.enums
*/

class CencSchemeName implements IKalturaPluginEnum, DrmSchemeName
{
	const PLAYREADY_CENC = 'PLAYREADY_CENC';
	const WIDEVINE_CENC = 'WIDEVINE_CENC';

	public static function getAdditionalValues()
	{   
		return array
		(
			'PLAYREADY_CENC' => self::PLAYREADY_CENC,
			'WIDEVINE_CENC' => self::WIDEVINE_CENC,
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