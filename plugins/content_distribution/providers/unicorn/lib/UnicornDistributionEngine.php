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
		$serviceUrl = trim(KBatchBase::$kClientConfig->serviceUrl, '/');
		return "$serviceUrl/api_v3/index.php/service/unicornDistribution_unicorn/action/notify/partnerId/{$job->partnerId}/id/{$job->id}";
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
		
		$flavorAssetIds = explode(',', $entryDistribution->flavorAssetIds);
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
		KalturaLog::debug("XML [$xml]");
		
		
		$ch = curl_init($distributionProfile->apiHostUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
		$response = curl_exec($ch);
		
		
		if(!$response)
		{
			$curlError = curl_error($ch);
			$curlErrorNumber = curl_errno($ch);
			curl_close($ch);
			throw new KalturaDispatcherException("HTTP request failed: $curlError", $curlErrorNumber);
		}
		curl_close($ch);
		KalturaLog::debug("Response [$response]");
	
		$matches = null;
		if(preg_match_all('/HTTP\/?[\d.]{0,3} ([\d]{3}) ([^\n\r]+)/', $response, $matches))
		{
			KalturaLog::debug("Matches [" . print_r($matches, true) . "]");
			foreach($matches[0] as $index => $match)
			{
				$code = intval($matches[1][$index]);
				$message = $matches[2][$index];
			
				if($code == KCurlHeaderResponse::HTTP_STATUS_CONTINUE)
				{
					continue;
				}
				
				if($code != KCurlHeaderResponse::HTTP_STATUS_OK)
				{
					throw new KalturaDistributionException("HTTP response code [$code] error: $message", $code);
				}
				
				if(preg_match('/^MediaItemGuid: (.+)$/', $message, $matches))
				{
					$data->remoteId = $matches[1];
					KalturaLog::debug("Remote ID [$data->remoteId]");
				}
				
				return;
			}
		}
		else
		{
			throw new KalturaDistributionException("Unexpected HTTP response");
		}
	}
}