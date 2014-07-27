<?php


class WSLiveStats extends WSBaseObject
{				
	function getKalturaObject() {
		return new KalturaLiveStats();
	}
	
	/**
	 * @var long
	 **/
	public $audience;
	
	/**
	 * @var float
	 **/
	public $avgBitrate;
	
	/**
	 * @var long
	 **/
	public $bufferTime;
	
	/**
	 * @var long
	 **/
	public $plays;
	
	/**
	 * @var long
	 **/
	public $secondsViewed;
	
	/**
	 * @var long
	 **/
	public $startEvent;
	
	/**
	 * @var long
	 **/
	public $timestamp;
	
}


