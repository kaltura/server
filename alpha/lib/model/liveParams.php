<?php

/**
 * Subclass for representing a row from the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class liveParams extends flavorParams
{
	const CUSTOM_DATA_FIELD_STREAM_SUFFIX = "streamSuffix";
	
	public function getStreamSuffix() {
		return $this->getFromCustomData ( liveParams::CUSTOM_DATA_FIELD_STREAM_SUFFIX );
	}
	
	public function setStreamSuffix($v) {
		$this->putInCustomData ( liveParams::CUSTOM_DATA_FIELD_STREAM_SUFFIX, $v );
	}
}
