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
			case 'objects':
				return 'WSLiveStatsArray';
			default:
				return parent::getAttributeType($attributeName);
		}
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


