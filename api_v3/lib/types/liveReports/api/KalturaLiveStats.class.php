<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStats extends KalturaObject
{				
	/**
	 *
	 * @var int
	 **/
	public $audience;
	
	/**
	 *
	 * @var float
	 **/
	public $avgBitrate;
	
	/**
	 *
	 * @var int
	 **/
	public $bufferTime;
	
	/**
	 *
	 * @var int
	 **/
	public $plays;
	
	/**
	 *
	 * @var int
	 **/
	public $secondsViewed;
	
	/**
	 *
	 * @var bigint
	 **/
	public $startEvent;
	
	/**
	 *
	 * @var time
	 **/
	public $timestamp;
	
	public function getWSObject() {
		$obj = new WSLiveStats();
		$obj->fromKalturaObject($this);
		return $obj;
	}
	
}


