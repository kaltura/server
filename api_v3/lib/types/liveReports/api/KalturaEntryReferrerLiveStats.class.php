<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryReferrerLiveStats extends KalturaEntryLiveStats
{			
	/**
	 * @var string
	 **/
	public $referrer;
	
	public function getWSObject() {
		$obj = new WSEntryReferrerLiveStats();
		$obj->fromKalturaObject($this);
		return $obj;
	}
}


