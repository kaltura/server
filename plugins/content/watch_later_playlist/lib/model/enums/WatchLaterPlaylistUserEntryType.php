<?php
/**
 * @package plugins.watchLaterPlaylist
 * @subpackage model.enum
 */
class WatchLaterPlaylistUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const WATCH_LATER_PLAYLIST = "WATCH_LATER_PLAYLIST";

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			"WATCH_LATER_PLAYLIST" => self::WATCH_LATER_PLAYLIST,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::WATCH_LATER_PLAYLIST => 'Watch Later Playlist User Entry Type',
		);
	}
}