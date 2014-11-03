<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryLiveStats extends KalturaLiveStats
{				
	/**
	 * @var string
	 **/
	public $entryId;
	
	/**
	 * @var int
	 */
	public $peakAudience;
	
	public function getWSObject() {
		$obj = new WSEntryLiveStats();
		$obj->fromKalturaObject($this);
		return $obj;
	}
	
}


