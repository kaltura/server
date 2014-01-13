<?php

require_once(__DIR__ . '/KalturaMediaServerClient.class.php');
	
class KalturaMediaServerLiveService extends KalturaMediaServerClient
{
	function __construct($url)
	{
		parent::__construct($url);
	}
	
	
	/**
	 * 
	 * @param string $liveEntryId
	 * @return KalturaMediaServerSplitRecordingNowResponse
	 **/
	public function splitRecordingNow($liveEntryId)
	{
		$params = array();
		
		$params["liveEntryId"] = $this->parseParam($liveEntryId, 'xsd:string');

		return $this->doCall("splitRecordingNow", $params, 'KalturaMediaServerSplitRecordingNowResponse');
	}
	
}		
	
