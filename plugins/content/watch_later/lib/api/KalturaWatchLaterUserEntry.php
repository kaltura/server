<?php
/**
 * @package plugins.watchLater
 * @subpackage api
 */
class KalturaWatchLaterUserEntry extends KalturaUserEntry
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
		{
			$dbObject = new WatchLaterUserEntry();
		}

		return parent::toObject($dbObject, $propertiesToSkip);
	}
}