<?php
/**
 * @package plugins.watchLaterPlaylist
 * @subpackage model
 */
class WatchLaterPlaylistUserEntry extends UserEntry
{
	const WATCH_LATER_PLAYLIST_OM_CLASS = 'WatchLaterPlaylistUserEntry';

	public function __construct()
	{
		$this->setType(WatchLaterPlaylistPlugin::getWatchLaterPlaylistUserEntryTypeCoreValue(WatchLaterPlaylistUserEntryType::WATCH_LATER_PLAYLIST));
		parent::__construct();
	}

}