<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMixEntryFilter extends KalturaMixEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::MIX;
	}
}
