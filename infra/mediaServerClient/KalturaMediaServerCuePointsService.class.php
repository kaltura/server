<?php

require_once(__DIR__ . '/KalturaMediaServerClient.class.php');
	
class KalturaMediaServerCuePointsService extends KalturaMediaServerClient
{
	function __construct($url)
	{
		parent::__construct($url);
	}
	
	
	/**
	 * 
	 * @param string $liveEntryId
	 * @param int $interval
	 * @param int $duration
	 * @return KalturaMediaServerCreateTimeCuePointsResponse
	 **/
	public function createTimeCuePoints($liveEntryId, $interval, $duration)
	{
		$params = array();
		
		$params["liveEntryId"] = $this->parseParam($liveEntryId, 'xsd:string');
		$params["interval"] = $this->parseParam($interval, 'xsd:int');
		$params["duration"] = $this->parseParam($duration, 'xsd:int');

		return $this->doCall("createTimeCuePoints", $params, 'KalturaMediaServerCreateTimeCuePointsResponse');
	}
	
}		
	
