<?php


class WSEntryReferrerLiveStats extends WSEntryLiveStats
{			
	function getKalturaObject() {
		return new KalturaEntryReferrerLiveStats();
	}
	
	/**
	 * @var string
	 **/
	public $referrer;
	
}


