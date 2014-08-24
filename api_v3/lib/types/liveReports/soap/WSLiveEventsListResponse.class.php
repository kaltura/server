<?php


class WSLiveEventsListResponse extends WSBaseObject
{				
	function getKalturaObject() {
		return new KalturaLiveEventsListResponse();
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'objects':
				return 'WSLiveEventsArray';
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


