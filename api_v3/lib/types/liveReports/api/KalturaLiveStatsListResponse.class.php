<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStatsListResponse extends KalturaListResponse
{				
	/**
	 *
	 * @var KalturaLiveStats
	 **/
	public $objects;
	
	public function getWSObject() {
		$obj = new WSLiveEntriesListResponse();
		$obj->fromKalturaObject($this);
		return $obj;
	}
	
}


