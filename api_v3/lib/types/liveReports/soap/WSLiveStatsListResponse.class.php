<?php


class WSLiveStatsListResponse extends WSBaseObject
{				
	function getKalturaObject() {
		return new KalturaLiveStatsListResponse();
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'events':
				return 'WSLiveStatsArray';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var array
	 **/
	public $events;
	
	/**
	 * @var int
	 **/
	public $totalCount;
	
}


