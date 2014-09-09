<?php


class WSLiveReportInputFilter extends WSBaseObject
{	
	function getKalturaObject() {
		return new KalturaLiveReportInputFilter();
	}
				
	/**
	 * @var string
	 **/
	public $entryIds;
	
	/**
	 * @var long
	 **/
	public $eventTime;
	
	/**
	 * @var long
	 **/
	public $fromTime;
	
	/**
	 * @var int
	 **/
	public $hoursBefore;
	
	/**
	 * @var boolean
	 **/
	public $live;
	
	/**
	 * @var long
	 **/
	public $partnerId;
	
	/**
	 * @var long
	 **/
	public $toTime;
	
	/**
	 * @var int
	 */
	public $resultsLimit;
	
}


