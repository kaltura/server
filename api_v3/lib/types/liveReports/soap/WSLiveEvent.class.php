<?php


class WSLiveEvent extends WSBaseObject
{				
	function getKalturaObject() {
		return new KalturaLiveEvent();
	}
	
	/**
	 * @var long
	 **/
	public $value;
	
	/**
	 * @var long
	 **/
	public $timestamp;
	
}


