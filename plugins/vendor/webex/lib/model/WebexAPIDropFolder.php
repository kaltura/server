<?php

/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class WebexAPIDropFolder extends RemoteDropFolder
{
	
	const WEBEXPAI_VENDOR_INTEGRATION_ID = 'webexapi_vendor_integration_id';
	const LAST_HANDLED_MEETING_TIME = 'last_handled_meeting_time';
	
	/**
	 * @var string
	 */
	protected $webexAPIVendorIntegrationId;
	
	/**
	 * @var time
	 */
	protected $lastHandledMeetingTime;
	
	/**
	 * return string
	 */
	public function getWebexAPIVendorIntegrationId()
	{
		return $this->getFromCustomData(self::WEBEXPAI_VENDOR_INTEGRATION_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexAPIVendorIntegrationId($v)
	{
		$this->putInCustomData(self::WEBEXPAI_VENDOR_INTEGRATION_ID, $v);
	}
	
	/**
	 * return time
	 */
	public function getLastHandledMeetingTime()
	{
		return $this->getFromCustomData(self::LAST_HANDLED_MEETING_TIME);
	}
	
	/**
	 * @param time $v
	 */
	public function setLastHandledMeetingTime($v)
	{
		$this->putInCustomData(self::LAST_HANDLED_MEETING_TIME, $v);
	}
	
	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}
	
	public function getFolderUrl()
	{
		return kConf ::getArrayValue(
			KalturaWebexAPIDropFolder::WEBEX_BASE_URL, KalturaWebexAPIDropFolder::CONFIGURATION_PARAM_NAME, KalturaWebexAPIDropFolder::MAP_NAME
		);
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::WEBEX_API; /// ????
	}
}