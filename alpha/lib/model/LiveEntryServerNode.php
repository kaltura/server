<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNode extends EntryServerNode{

	const OM_CLASS = 'LiveEntryServerNode';
	
	const CUSTOM_DATA_STREAMS = "streams";
	const CUSTOM_DATA_APPLICATION_NAME = "application_name";

	public function setStreams(KalturaLiveStreamParamsArray $v) 
	{ 
		$this->putInCustomData(self::CUSTOM_DATA_STREAMS, $v); 
	}
	
	public function getStreams()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_STREAMS);
	}
	
	public function setApplicationName($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APPLICATION_NAME, $v);
	}
	
	public function getApplicationName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APPLICATION_NAME);
	}
}