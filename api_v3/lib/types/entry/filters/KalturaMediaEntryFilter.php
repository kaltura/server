<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaEntryFilter extends KalturaMediaEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::MEDIA_CLIP;
	}
}
