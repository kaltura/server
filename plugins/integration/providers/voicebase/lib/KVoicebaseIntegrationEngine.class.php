<?php
/**
 * @package plugins.voicebase
 * @subpackage Scheduler
 */
class KVoicebaseIntegrationEngine implements KIntegrationCloserEngine
{
	private $baseEndpointUrl = null;
	private $clientHelper = null;
	
	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::dispatch()
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doDispatch($job, $data, $data->providerData);
	}
	
	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doClose($job, $data, $data->providerData);
	}
	
	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVoicebaseJobProviderData $providerData)
	{
		$entryId = $providerData->entryId;
		$flavorAssetId = $providerData->flavorAssetId;
		$spokenLanguage = $providerData->spokenLanguage;
		$formatsString = $providerData->captionAssetFormats;
		$formatsArray = explode(',', $formatsString);

		$shouldReplaceRemoteMedia = $providerData->replaceMediaContent;
		$fileLocation = $providerData->fileLocation;
		$callBackUrl = $data->callbackNotificationUrl;
	
		KalturaLog::debug('callback is - ' . $callBackUrl);	
	
		$this->clientHelper = VoicebasePlugin::getClientHelper($providerData->apiKey, $providerData->apiPassword);
	
		$flavorAssetId = $this->validateFlavorAssetId($entryId, $flavorAssetId);
		$flavorUrl = KBatchBase::$kClient->flavorAsset->getUrl($flavorAssetId);
	
		$externalEntryExists = $this->clientHelper->checkExitingExternalContent($entryId);
		if (!$externalEntryExists)
		{
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $fileLocation);
			if(!$uploadSuccess)
				throw new Exception("upload failed");
		}
		elseif($shouldReplaceRemoteMedia == true)
		{
			$this->clientHelper->deleteRemoteFile($entryId);
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $fileLocation);
			if(!$uploadSuccess)
				throw new Exception("upload failed");
		}
		elseif($fileLocation)
		{
			$this->clientHelper->updateRemoteTranscript($entryId, $fileLocation, $callBackUrl);
		}	
		else
		{
			return true;
		}

		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVoicebaseJobProviderData $providerData)
	{
		return false;
	}
	
	private function validateFlavorAssetId($entryId, $flavorAssetId = null)
	{
		$sourceAssetId = null;
	
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entryId;
		$pager = null;
		$assetsobjectList = KBatchBase::$kClient->flavorAsset->listAction($filter, $pager);
	
		foreach($assetsobjectList->objects as $entryAsset)
		{
			if($flavorAssetId && $entryAsset->id == $flavorAssetId)
				return $flavorAssetId;
	
			if($entryAsset->isOriginal == 1)
				$sourceAssetId = $entryAsset->id;
		}
	
		return $sourceAssetId;
	}
}
