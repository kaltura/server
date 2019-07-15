<?php
/**
 * @package plugins.watchLaterPlaylist
 * @subpackage api.errors
 */

class KalturaWatchLaterPlaylistErrors extends KalturaErrors
{
	const PLAYLIST_ID_NOT_GIVEN = 'PLAYLIST_ID_NOT_GIVEN;;No playlist id given';
	const ENTRY_ID_ALREADY_EXISTS_IN_USER_ENTRY_PLAYLIST = 'ENTRY_ID_ALREADY_EXISTS_IN_USER_ENTRY_PLAYLIST;ENTRY_ID;A watch later playlist user-entry for the 
		given user-id and entry-id [@ENTRY_ID@] already exists, cannot create duplicate';
}
