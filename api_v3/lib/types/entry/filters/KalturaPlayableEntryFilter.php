<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPlayableEntryFilter extends KalturaPlayableEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::MEDIA_CLIP . ',' . KalturaEntryType::MIX . ',' . KalturaEntryType::LIVE_STREAM . ',' . KalturaEntryType::LIVE_CHANNEL;
	}
}
