<?php
/**
 * @package plugins.watchLaterPlaylist
 * @subpackage api
 */
class KalturaWatchLaterPlaylistUserEntry extends KalturaUserEntry
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
		{
			$dbObject = new WatchLaterPlaylistUserEntry();
		}

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}