<?php

/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class ZoomDropFolder extends RemoteDropFolder
{
	
	const ZOOM_VENDOR_INTEGRATION_ID = 'zoom_vendor_integration_id';
	const LAST_HANDLED_MEETING_TIME = 'last_handled_meeting_time';
	
	/**
	 * @var string
	 */
	protected $zoomVendorIntegrationId;
	
	/**
	 * @var time
	 */
	protected $lastHandledMeetingTime;
	
	/**
	 * return string
	 */
	public function getZoomVendorIntegrationId()
	{
		return $this->getFromCustomData(self::ZOOM_VENDOR_INTEGRATION_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setZoomVendorIntegrationId($v)
	{
		$this->putInCustomData(self::ZOOM_VENDOR_INTEGRATION_ID, $v);
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
			KalturaZoomDropFolder::ZOOM_BASE_URL, ZoomHelper::ZOOM_ACCOUNT_PARAM, ZoomHelper::VENDOR_MAP
		);
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::ZOOM; /// TODO
	}
}