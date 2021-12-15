<?php


class onedriveMgr extends httpMgr
{
	const EXPIRY_WINDOW = 60;
	
	public function __construct(array $options = null)
	{
		parent::__construct($options);
	}
	
	public function refreshUrl($url, KalturaImportJobData $jobData)
	{
		if ($jobData->expiry - self::EXPIRY_WINDOW > time())
		{
			return $url;
		}
		
		KalturaLog::info('Graph API token for url has expired');
		$vendorPlugin = KalturaVendorClientPlugin::get(KBatchBase::$kClient);
		$vendorIntegrationSetting = $vendorPlugin->vendorIntegration->get($jobData->vendorIntegrationId);
		$this->graphClient = new KMicrosoftGraphApiClient($vendorIntegrationSetting->accountId, $vendorIntegrationSetting->clientId, $vendorIntegrationSetting->clientSecret);
		$item = $this->graphClient->getDriveItem($jobData->driveId, $jobData->itemId);
		if (!isset($item[MicrosoftGraphFieldNames::DOWNLOAD_URL]))
		{
			return $url;
		}
		
		return $item[MicrosoftGraphFieldNames::DOWNLOAD_URL];
	}
}