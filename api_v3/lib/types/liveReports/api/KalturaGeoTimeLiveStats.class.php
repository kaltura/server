<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaGeoTimeLiveStats extends KalturaEntryLiveStats
{	
	/**
	 * @var KalturaCoordinate
	 **/
	public $city;
	
	/**
	 * @var KalturaCoordinate
	 **/
	public $country;
	
	public function getWSObject() {
		$obj = new WSGeoTimeLiveStats();
		$obj->fromKalturaObject($this);
		return $obj;
	}
}


