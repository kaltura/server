<?php

class WSLiveStatsArray extends SoapArray {
	
	public function __construct()
	{
		parent::__construct("WSLiveStats");
	}
	
	protected function getClass($object = null) {
		if(is_null($object))
			return null;
		switch($object['__object_type__']) {
			case "geoTimeLiveStats":
				return 'WSGeoTimeLiveStats';
			case "entryReferrerLiveStats":
				return 'WSEntryReferrerLiveStats';
			case "entryLiveStats":
				return 'WSEntryLiveStats';
			default:
				return 'WSLiveStats';
		}
	}
	
	public function fromArray(array $result)
	{
		// Hack to handle the case in which array of size '1' is returned without array wrapping.
		if(!array_key_exists(0, $result)) {
			$class = $this->getClass($result);
			$obj = new $class();
			$obj->fromArray($result);
			$this[] = $obj;
		} else { 
			return parent::fromArray($result);
		}
	}
}

