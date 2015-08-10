<?php
class kVoicebaseFlowManager implements kBatchJobStatusEventConsumer 
{
	private $baseEndpointUrl = null;
	const DEFAULT_ACCURACY = 60;
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if(in_array($dbBatchJob->getStatus(), array(BatchJob::BATCHJOB_STATUS_DONT_PROCESS, BatchJob::BATCHJOB_STATUS_FINISHED)) && $dbBatchJob->getJobType() == IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
		{
			$providerType = $dbBatchJob->getJobSubType();
			if ($providerType == VoicebasePlugin::getProviderTypeCoreValue(VoicebaseIntegrationProviderType::VOICEBASE))
				return true;
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{	
		$data = $dbBatchJob->getData();
		$providerData = $data->getProviderData();
		$entryId = $providerData->getEntryId();
		$partnerId = $dbBatchJob->getPartnerId();
		$spokenLanguage = $providerData->getSpokenLanguage();
	
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_DONT_PROCESS)
		{
			$transcript = $this->getAssetsByLanguage($entryId, array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)), $spokenLanguage, true);	
			if(!$transcript)
			{
				$transcript = new TranscriptAsset();
				$transcript->setType(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
				$transcript->setEntryId($entryId);
				$transcript->setPartnerId($partnerId);
				$transcript->setLanguage($spokenLanguage);
				$transcript->setAccuracy(self::DEFAULT_ACCURACY);
			}
			$transcript->setStatus(AttachmentAsset::ASSET_STATUS_QUEUED);
			$transcript->save();
	
			return true;
		}
	
		$formatsString = $providerData->getCaptionAssetFormats();		
	
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$clientHelper = VoicebasePlugin::getClientHelper($providerData->getApiKey(), $providerData->getApiPassword());
		
			$externalEntryExists = $clientHelper->checkExitingExternalContent($entryId);
			if (!$externalEntryExists)
			{
				KalturaLog::err('remote content does not exist');
				return true;     	
			}
			$formatsArray = explode(',',$formatsString);
			$formatsArray[] = "TXT";
			$contentsArray = $clientHelper->getRemoteTranscripts($entryId, $formatsArray);
			KalturaLog::debug('contents are - ' . print_r($contentsArray, true));
			$transcript = $this->getAssetsByLanguage($entryId, array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)), $spokenLanguage, true);
			$captions = $this->getAssetsByLanguage($entryId, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)), $spokenLanguage);
		
			$this->setObjectContent($transcript, $contentsArray["TXT"]);
			unset($contentsArray["TXT"]);
	
			foreach ($contentsArray as $format => $content)
			{        
				$captionFormatConst = constant("KalturaCaptionType::" . $format);
				if(isset($captions[$captionFormatConst]))
					$caption = $captions[$captionFormatConst];
				else
				{
					$caption = new CaptionAsset();
					$caption->setEntryId($entryId);
					$caption->setPartnerId($partnerId);
					$caption->setLanguage($spokenLanguage);
					$caption->setContainerFormat($captionFormatConst);
					$caption->setAccuracy(self::DEFAULT_ACCURACY);
					$caption->setStatus(CaptionAsset::ASSET_STATUS_QUEUED);
					$caption->save();
				}
				$this->setObjectContent($caption, $content, $format);
			}
		}
		return true;					    
	}
	
	function getAssetsByLanguage($entryId, array $assetTypes, $spokenLanguage, $returnSingleObject = false)
	{
		$objects = $returnSingleObject ? null : array();
		$statuses = array(asset::ASSET_STATUS_QUEUED, asset::ASSET_STATUS_READY);
		$resultArray = assetPeer::retrieveByEntryId($entryId, $assetTypes, $statuses);
	
		foreach($resultArray as $object)
		{
			if($object->getLanguage() == $spokenLanguage)
			{
				if ($returnSingleObject)
					return $object;
				$objects[$object->getContainerFormat()] = $object;
			}
		}	
		return $objects;
	}
	
	private function setObjectContent($assetObject, $content, $format = null)
	{
		$assetObject->incrementVersion();
		$ext = "txt";
		if($format)
		{
			if ($format == "DFXP")
				$ext = "xml";
			if ($format == "SRT")
				$ext = "srt";
		}
	
		$assetObject->setFileExt($ext);
		$assetObject->save();
		$syncKey = $assetObject->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
	
		kFileSyncUtils::file_put_contents($syncKey, $content); 		
	
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$assetObject->setSize(kFile::fileSize($finalPath));
	
		$assetObject->setStatus(AttachmentAsset::ASSET_STATUS_READY);
		$assetObject->save();
	} 
}
