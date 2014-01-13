<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveStreamEntryFilter extends KalturaLiveStreamEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::LIVE_STREAM;
	}
}
