<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage lib
 */
class UnicornDistributionEngine extends DistributionEngine implements IDistributionEngineUpdate, IDistributionEngineSubmit, IDistributionEngineCloseSubmit, IDistributionEngineCloseUpdate
{
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaUnicornDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaUnicornDistributionProfile");
		
		if(!$data->providerData || !($data->providerData instanceof KalturaUnicornDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUnicornDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaUnicornDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaUnicornDistributionProfile");
		
		if(!$data->providerData || !($data->providerData instanceof KalturaUnicornDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUnicornDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		// will be closed by the callback notification
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		// will be closed by the callback notification
		return false;
	}
	
	protected function getNotificationUrl()
	{
		$job = KJobHandlerWorker::getCurrentJob();
		$urlParams = array('service' => 'unicornDistribution_unicorn', 'action' => 'notify', 'partnerId' => $job->partnerId, 'id' => $job->id);
		return requestUtils::getRequestHost() . '/api_v3/index.php/' . requestUtils::buildRequestParams($urlParams);
	}
	
	/**
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $assetIds comma seperated
	 * @return array<KalturaCaptionAsset>
	 */
	protected function getCaptionAssets($partnerId, $entryId, $assetIds)
	{
		KBatchBase::impersonate($partnerId);
		$filter = new KalturaCaptionAssetFilter();
		$filter->entryIdEqual = $entryId;
		$filter->idIn = $assetIds;
		
		$captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		$assetsList = $captionPlugin->captionAsset->listAction($filter);
		KBatchBase::unimpersonate();
		
		return $assetsList->objects;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaUnicornDistributionProfile $distributionProfile
	 * @param KalturaUnicornDistributionJobProviderData $providerData
	 * @return string
	 */
	protected function buildXml(KalturaDistributionJobData $data, KalturaUnicornDistributionProfile $distributionProfile, KalturaUnicornDistributionJobProviderData $providerData)
	{
		$entryDistribution = $data->entryDistribution;
		/* @var $entryDistribution KalturaEntryDistribution */
		
		$flavorAssetIds = implode(',', $entryDistribution->flavorAssetIds);
		$flavorAssetId = reset($flavorAssetIds);
		$downloadURL = $this->getFlavorAssetUrl($flavorAssetId);
		
		$xml = new SimpleXMLElement('<APIIngestRequest/>');
		$xml->addChild('UserName', $distributionProfile->username);
		$xml->addChild('Password', $distributionProfile->password);
		$xml->addChild('DomainName', $distributionProfile->domainName);
		
		$avItemXml = $xml->addChild('AVItem');
		$avItemXml->addChild('CatalogGUID', $providerData->catalogGUID);
		$avItemXml->addChild('ForeignKey', $entryDistribution->entryId);
		$avItemXml->addChild('IngestItemType', 'Video');
		
		$ingestInfoXml = $avItemXml->addChild('IngestInfo');
		$ingestInfoXml->addChild('DownloadURL', $downloadURL);
		
		$avItemXml->addChild('Title', $providerData->title);
		
		if($entryDistribution->assetIds)
		{
			$captionsXml = $avItemXml->addChild('Captions');
			
			$captions = $this->getCaptionAssets($entryDistribution->partnerId, $entryDistribution->entryId, $entryDistribution->assetIds);
			foreach($captions as $caption)
			{
				/* @var $caption KalturaCaptionAsset */
				$captionXml = $captionsXml->addChild('Caption');
				$captionXml->addChild('ForeignKey', $caption->id);
				
				$ingestInfoXml = $captionXml->addChild('IngestInfo');
				$ingestInfoXml->addChild('DownloadURL', $this->getFlavorAssetUrl($caption->id));
				
				$captionXml->addChild('Language', $caption->languageCode);
			}
		}
		
		$xml->addChild('NotificationURL', $this->getNotificationUrl());
		$xml->addChild('NotificationRequestMethod', 'GET');
		
		return $xml->asXML();
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaUnicornDistributionProfile $distributionProfile
	 * @param KalturaUnicornDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaUnicornDistributionProfile $distributionProfile, KalturaUnicornDistributionJobProviderData $providerData)
	{
		$xml = $this->buildXml($data, $distributionProfile, $providerData);
		
		$curl = new KCurlWrapper();
		$curl->setOpt(CURLOPT_POST, true);
		$curl->setOpt(CURLOPT_POSTFIELDS, $xml);
		$curl->setOpt(CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
		$response = $curl->getHeader($distributionProfile->apiHostUrl);
		
		if(!$response)
		{
			$curlError = $curl->getError();
			$curlErrorNumber = $curl->getErrorNumber();
			$curl->close();
			throw new KalturaDispatcherException("HTTP request failed: $curlError", $curlErrorNumber);
		}
		$curl->close();
		
		if($response->code != KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			throw new KalturaDispatcherException("HTTP response code error: $response->codeName", $response->code);
		}
	}
}