<?php
/**
 * @package plugins.watchLater
 * @subpackage model
 */
class WatchLaterUserEntry extends UserEntry
{
	const WATCH_LATER_OM_CLASS = 'WatchLaterUserEntry';

	public function __construct()
	{
		$this->setType(WatchLaterPlugin::getWatchLaterUserEntryTypeCoreValue(WatchLaterUserEntryType::WATCH_LATER));
		parent::__construct();
	}
}