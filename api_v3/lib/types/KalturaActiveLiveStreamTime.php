<?php

/**
* @package api
* @subpackage objects
*/
class KalturaActiveLiveStreamTime extends KalturaObject
{
	function __construct($startTime, $endTime)
	{
		$this->startTime = $startTime;
		$this->endTime = $endTime;
	}

	/**
	* The start time of the live stream (unix timestamp in seconds)
	* @var int
	*/
	public $startTime;

	/**
	* The end time of the live stream (unix timestamp in seconds)
	* @var int
	*/
	public $endTime;
}
