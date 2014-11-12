<?php


class WSLiveEventsListResponse extends WSBaseObject
{				
	function getKalturaObject() {
		return new KalturaLiveEventsListResponse();
	}
	
	/**
	 * @var array
	 **/
	public $objects;
	
	/**
	 * @var int
	 **/
	public $totalCount;
	
}


