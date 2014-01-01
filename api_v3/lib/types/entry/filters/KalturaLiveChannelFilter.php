<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveChannelFilter extends KalturaLiveChannelBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::LIVE_CHANNEL;
	}
}
