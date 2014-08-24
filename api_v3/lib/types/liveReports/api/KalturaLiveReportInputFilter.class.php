<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveReportInputFilter extends KalturaObject
{	
	/**
	 * @var string
	 **/
	public $entryIds;
	
	/**
	 * @var time
	 **/
	public $eventTime;
	
	/**
	 * @var time
	 **/
	public $fromTime;
	
	/**
	 * @var int
	 **/
	public $hoursBefore;
	
	/**
	 * @var KalturaNullableBoolean
	 **/
	public $live;
	
	/**
	 * @var int
	 **/
	public $partnerId;
	
	/**
	 * @var time
	 **/
	public $toTime;
	
	/**
	 * @var int
	 */
	public $resultsLimit;
	
	public function getWSObject() {
		$obj = new WSLiveReportInputFilter();
		$obj->fromKalturaObject($this);
		return $obj;
	}
}


