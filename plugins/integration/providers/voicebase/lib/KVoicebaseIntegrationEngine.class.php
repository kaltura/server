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
		KalturaLog::info ("Starting dispatch - VoiceBase");
		$entryId = $providerData->entryId;
		$flavorAssetId = $providerData->flavorAssetId;
		$spokenLanguage = $providerData->spokenLanguage;
		$formatsString = $providerData->captionAssetFormats;
		$formatsArray = explode(',', $formatsString);

		$shouldReplaceRemoteMedia = $providerData->replaceMediaContent;
		$fileLocation = $providerData->fileLocation;
		$callBackUrl = $data->callbackNotificationUrl;
	
		KalturaLog::debug('callback is - ' . $callBackUrl);

		$additionalParameters = json_decode($providerData->additionalParameters, true);
		$this->clientHelper = VoicebasePlugin::getClientHelper($providerData->apiKey, $providerData->apiPassword, $additionalParameters);
		$flavorUrl = KBatchBase::$kClient->flavorAsset->getUrl($flavorAssetId);
	
		$externalEntryExists = $this->clientHelper->checkExistingExternalContent($entryId);
		if (!$externalEntryExists)
		{
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $fileLocation);
		}
		elseif($shouldReplaceRemoteMedia == true)
		{
			$this->clientHelper->deleteRemoteFile($entryId);
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $fileLocation);

		}
		elseif($fileLocation)
		{
			$result = $this->clientHelper->updateRemoteTranscript($entryId, $fileLocation, $callBackUrl);
		}	
		else
		{
			return true;
		}

		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVoicebaseJobProviderData $providerData)
	{
		$entryId = $providerData->entryId;
		$this->clientHelper = VoicebasePlugin::getClientHelper($providerData->apiKey, $providerData->apiPassword);
		$remoteProcess = $this->clientHelper->retrieveRemoteProcess($entryId);
		
		//false result means that something has gone wrong - the VB job is either in status error or missing altogether
		if(!$remoteProcess || $remoteProcess->requestStatus == VoicebaseClientHelper::VOICEBASE_FAILURE_MESSAGE || !isset($remoteProcess->fileStatus) || $remoteProcess->fileStatus == VoicebaseClientHelper::VOICEBASE_MACHINE_FAILURE_MESSAGE)
		{
			throw new Exception("VoiceBase transcription failed. Message: [" . $remoteProcess->response . "]");
		}
		
		if($remoteProcess && $remoteProcess->fileStatus == VoicebaseClientHelper::VOICEBASE_MACHINE_COMPLETE_MESSAGE && $remoteProcess->requestStatus == VoicebaseClientHelper::VOICEBASE_MACHINE_COMPLETE_REQUEST_STATUS)
		{
			return true;
		}
		
		return false;
	}
}
