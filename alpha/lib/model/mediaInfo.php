<?php

/**
 * Subclass for representing a row from the 'media_info' table.
 *
 * 
 *
 * @package lib.model
 */ 
class mediaInfo extends BasemediaInfo
{
	const ASSET_TYPE_ENTRY_INPUT = 0;
	const ASSET_TYPE_FLAVOR_INPUT = 1;
	
	const MEDIA_INFO_BIT_RATE_MODE_CBR = 1;
	const MEDIA_INFO_BIT_RATE_MODE_VBR = 2; 
}
