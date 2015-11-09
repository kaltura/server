<?php


class WSLiveReportInputPager extends WSBaseObject
{	
	function getKalturaObject() {
		return null;
	}
				
	/**
	 * @var int
	 **/
	public $pageSize;
	
	/**
	 * @var int
	 **/
	public $pageIndex;	
	
	public function __construct($pageSize, $pageIndex) {
		$this->pageSize = $pageSize;
		$this->pageIndex = $pageIndex;
	}
}


