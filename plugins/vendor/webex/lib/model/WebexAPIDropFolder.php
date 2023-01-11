<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class WebexAPIDropFolder extends RemoteDropFolder
{
	const WEBEX_API_VENDOR_INTEGRATION_ID = 'webexapi_vendor_integration_id';
	
	/**
	 * @var string
	 */
	protected $webexAPIVendorIntegrationId;
	
	/**
	 * return string
	 */
	public function getWebexAPIVendorIntegrationId()
	{
		return $this->getFromCustomData(self::WEBEX_API_VENDOR_INTEGRATION_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexAPIVendorIntegrationId($v)
	{
		$this->putInCustomData(self::WEBEX_API_VENDOR_INTEGRATION_ID, $v);
	}

	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}
	
	public function getFolderUrl()
	{
		return kConf::getArrayValue(
			WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_BASE_URL, WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_ACCOUNT_PARAM, WebexAPIDropFolderPlugin::CONFIGURATION_VENDOR_MAP
		);
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::WEBEX_API;
	}
}
