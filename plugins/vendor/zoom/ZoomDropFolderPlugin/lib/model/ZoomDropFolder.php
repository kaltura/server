<?php

/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class ZoomDropFolder extends RemoteDropFolder
{
	
	const ZOOM_VENDOR_INTEGRATION_ID = 'zoom_vendor_integration_id';
	const LAST_HANDLED_MEETING_TIME = 'last_handled_meeting_time';
	const FILE_PROCESSING_GRACE_PERIOD = 'file_processing_grace_period';

	const FILE_PROCESSING_GRACE_PERIOD_DEFAULT_VALUE = 10800; // 10800 seconds = 3 hours

	/**
	 * @var string
	 */
	protected $zoomVendorIntegrationId;
	
	/**
	 * @var time
	 */
	protected $lastHandledMeetingTime;

	/**
	 * @var int
	 */
	protected $fileProcessingGracePeriod;
	
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

	/**
	 * @return int
	 */
	public function getFileProcessingGracePeriod()
	{
		$value = $this->getFromCustomData(self::FILE_PROCESSING_GRACE_PERIOD);
		if (is_null($value)) {
			return self::FILE_PROCESSING_GRACE_PERIOD_DEFAULT_VALUE;
		}
		return $value;
	}

	/**
	 * @param int $v
	 */
	public function setFileProcessingGracePeriod($v)
	{
		$this->putInCustomData(self::FILE_PROCESSING_GRACE_PERIOD, $v);
	}

	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$ret = parent::preInsert($con);
		return $ret;
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
