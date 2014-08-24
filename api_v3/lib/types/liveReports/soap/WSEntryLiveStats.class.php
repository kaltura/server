<?php


class WSEntryLiveStats extends WSLiveStats
{				
	function getKalturaObject() {
		return new KalturaEntryLiveStats();
	}
	
	/**
	 * @var string
	 **/
	public $entryId;
	
}


