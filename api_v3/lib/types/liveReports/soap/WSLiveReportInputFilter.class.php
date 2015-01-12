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
	public $fromTime;
	
	/**
	 * @var long
	 **/
	public $toTime;
	
	/**
	 * @var boolean
	 **/
	public $live;
	
	/**
	 * @var long
	 **/
	public $partnerId;
	
	/**
	 * @var string
	 */
	public $orderBy;
	
}


