<?php
/**
 * @package plugins.watchLater
 * @subpackage model.enum
 */
class WatchLaterUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const WATCH_LATER = 'WATCH_LATER';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'WATCH_LATER' => self::WATCH_LATER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::WATCH_LATER => 'Watch Later User Entry Type',
		);
	}
}