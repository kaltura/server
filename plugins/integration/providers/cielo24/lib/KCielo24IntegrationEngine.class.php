<?php
/**
 * @package plugins.cielo24
 * @subpackage Scheduler
 */
class KCielo24IntegrationEngine implements KIntegrationCloserEngine
{
	private $baseEndpointUrl = null;
	private $clientHelper = null;
	
	const GET_URL_FILE_NAME = "kalturaFile";
	
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
	
	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaCielo24JobProviderData $providerData)
	{
		$entryId = $providerData->entryId;
		$flavorAssetId = $providerData->flavorAssetId;
		$spokenLanguage = $providerData->spokenLanguage;
		$priority = $providerData->priority;
		$fidelity = $providerData->fidelity;
	
		$formatsString = $providerData->captionAssetFormats;
		$formatsArray = explode(',', $formatsString);
	
		$shouldReplaceRemoteMedia = $providerData->replaceMediaContent;
		$callBackUrl = $data->callbackNotificationUrl;
		KalturaLog::debug('callback is - ' . $callBackUrl);	
	
		$this->clientHelper = Cielo24Plugin::getClientHelper($providerData->username, $providerData->password);
		
		//setting a pre-defined name to prevent the flavor-url to contain chars that will break the curl url syntax
		$nameOptions = new KalturaFlavorAssetUrlOptions();
		$nameOptions->fileName = self::GET_URL_FILE_NAME;	
		$flavorUrl = KBatchBase::$kClient->flavorAsset->getUrl($flavorAssetId, null, null, $nameOptions);
	
		$remoteJobId = $this->clientHelper->getRemoteFinishedJobId($entryId);
		if (!$remoteJobId)
		{
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $priority, $fidelity);
			if(!$uploadSuccess)
				throw new Exception("upload failed");
		}
		elseif($shouldReplaceRemoteMedia == true)
		{
			$this->clientHelper->deleteRemoteFile($remoteJobId);
			$uploadSuccess = $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $priority, $fidelity);
			if(!$uploadSuccess)
				throw new Exception("upload failed");
		}	
		else
		{
			return true;
		}
	
		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaCielo24JobProviderData $providerData)
	{
		return false;
	}
}
