<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveEntryServerNodeFilter extends KalturaLiveEntryServerNodeBaseFilter
{
	public function __construct()
	{
		$this->typeIn = array(KalturaEntryServerNodeType::LIVE_PRIMARY, KalturaEntryServerNodeType::LIVE_BACKUP);
	}
}
