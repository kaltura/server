<?php

/*
* @package plugins.playReady
* @subpackage model.enums
*/

class PlayReadySchemeName implements IKalturaPluginEnum, DrmSchemeName
{
	const PLAYREADY = 'PLAYREADY';

	public static function getAdditionalValues()
	{
		return array
		(
			'PLAYREADY' => self::PLAYREADY,
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