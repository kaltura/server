<?php

/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class ZoomDropFolder extends RemoteDropFolder
{
	
	const ZOOM_VENDOR_INTEGRATION_ID = 'zoom_vendor_integration_id';
	
	/**
	 * @var string
	 */
	protected $zoomVendorIntegrationId;
	
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
	
	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}
	
	public function getFolderUrl()
	{
		return kConf ::getArrayValue(
			KalturaZoomDropFolder::ZOOM_BASE_URL, KalturaZoomDropFolder::CONFIGURATION_PARAM_NAME, KalturaZoomDropFolder::MAP_NAME
		);
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::ZOOM; /// TODO
	}
}