<?php


class WSGeoTimeLiveStats extends WSEntryLiveStats
{	
	function getKalturaObject() {
		return new KalturaGeoTimeLiveStats();
	}
				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'city':
			case 'country':
				return 'WSCoordinate';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var WScoordinate
	 **/
	public $city;
	
	/**
	 * @var WScoordinate
	 **/
	public $country;
	
}


