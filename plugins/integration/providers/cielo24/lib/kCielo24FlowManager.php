<?php
class kCielo24FlowManager implements kBatchJobStatusEventConsumer 
{
	private $baseEndpointUrl = null;
	const FILE_NAME_PATTERN = "{entryId}-Transcript-{language}.txt";
	const MECHNICAL_TRANSCRIPTION_ACCURACY_VALUE = 0.7;
	const PREMIUM_TRANSCRIPTION_ACCURACY_VALUE = 0.95;
	const PROFESSIONAL_TRANSCRIPTION_ACCURACY_VALUE = 0.99;
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if(in_array($dbBatchJob->getStatus(), array(BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_DONT_PROCESS, BatchJob::BATCHJOB_STATUS_FINISHED)) && $dbBatchJob->getJobType() == IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
		{
			$providerType = $dbBatchJob->getJobSubType();
			if ($providerType == Cielo24Plugin::getProviderTypeCoreValue(Cielo24IntegrationProviderType::CIELO24))
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
	
		$transcript = $this->getAssetsByLanguage($entryId, array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)), $spokenLanguage, true);
		
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED)
		{
			if($transcript)
			{
				$transcript->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_ERROR);
				$transcript->save();
			}
		}
		
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_DONT_PROCESS)
		{	
			if(!$transcript)
			{
				$transcript = new TranscriptAsset();
				$transcript->setType(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
				$transcript->setEntryId($entryId);
				$transcript->setPartnerId($partnerId);
				$transcript->setLanguage($spokenLanguage);
				$transcript->setContainerFormat(AttachmentType::TEXT);
			}
			$transcript->setStatus(AttachmentAsset::ASSET_STATUS_QUEUED);
			$transcript->save();
	
			return true;
		}
	
		$formatsString = $providerData->getCaptionAssetFormats();		
	
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$clientHelper = Cielo24Plugin::getClientHelper($providerData->getUsername(), $providerData->getPassword(), $providerData->getBaseUrl());
		
			$languageName = $clientHelper->getLanguageConstantName($spokenLanguage);
			$jobId = $dbBatchJob->getId();
			$jobNameForSearch = "$entryId" . "_$languageName" . "_$jobId";

			$remoteJobId = $clientHelper->getRemoteJobIdByName($entryId, $jobNameForSearch);
			if (!$remoteJobId)
			{
				KalturaLog::err('remote content does not exist');
				$transcript->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_ERROR);
				$transcript->save();
				return true;     	
			}
	
			$formatsArray = explode(',',$formatsString);
			$transcriptContent = $clientHelper->getRemoteTranscript($remoteJobId);
			KalturaLog::debug("transcript content - $transcriptContent");
			$captionsContentArray = $clientHelper->getRemoteCaptions($remoteJobId, $formatsArray);
			KalturaLog::debug("captions content - " . print_r($captionsContentArray, true));
	
			$captions = $this->getAssetsByLanguage($entryId, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)), $spokenLanguage);
			switch ($providerData->getFidelity())
			{
				case KalturaCielo24Fidelity::MECHANICAL:
					$accuracyRate = self::MECHNICAL_TRANSCRIPTION_ACCURACY_VALUE;
					break;
				case KalturaCielo24Fidelity::PREMIUM:
					$accuracyRate = self::PREMIUM_TRANSCRIPTION_ACCURACY_VALUE;
					break;
				case KalturaCielo24Fidelity::PROFESSIONAL:
					$accuracyRate = self::PROFESSIONAL_TRANSCRIPTION_ACCURACY_VALUE;
					break;
			}

			$this->setObjectContent($transcript, $transcriptContent, $accuracyRate, null, true);
	
			foreach ($captionsContentArray as $format => $content)
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
					$caption->setStatus(CaptionAsset::ASSET_STATUS_QUEUED);
					$caption->save();
				}
				if ($captionFormatConst == KalturaCaptionType::DFXP)
				{
					$cielo24Options = Cielo24Plugin::getPartnerCielo24Options($partnerId);
					if ($cielo24Options->transformDfxp)
						$content = $this->transformDfxp($content);
				}
				$this->setObjectContent($caption, $content, $accuracyRate, $format);
			}
		}
		return true;					    
	}
	
	function getAssetsByLanguage($entryId, array $assetTypes, $spokenLanguage, $returnSingleObject = false)
	{
		$objects = $returnSingleObject ? null : array();
		$statuses = array(asset::ASSET_STATUS_QUEUED, asset::ASSET_STATUS_READY);
		if($returnSingleObject)
			$statuses[] = asset::FLAVOR_ASSET_STATUS_ERROR;
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
	
	private function setObjectContent($assetObject, $content, $accuracy, $format = null, $shouldSetTranscriptFileName = false)
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

		kEventsManager::raiseEvent(new kObjectDataChangedEvent($assetObject));
	
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		$assetObject->setSize(kFile::fileSize($finalPath));
	
		if ($shouldSetTranscriptFileName && !$assetObject->getFileName())
		{
			$language = str_replace(" ", "", $assetObject->getLanguage());
			
			$patterns = array("{entryId}","{language}");
			$replacements = array($assetObject->getEntryId(), $language);
			$fileName = str_replace($patterns, $replacements, self::FILE_NAME_PATTERN);
			$assetObject->setFileName($fileName);
		}
		
		$assetObject->setAccuracy($accuracy);
		$assetObject->setStatus(AttachmentAsset::ASSET_STATUS_READY);
		$assetObject->save();
	}
	
	private function transformDfxp($content)
	{
		$doc = new DOMDocument();
		/**
		* Replaces unescaped ampersands
		*
		* Ignores:
		* - Entity: &[anything];
		* - Dec: &#[numbers];
		* - Hex: &#x[numbers or A-F];
		*/
		$content = preg_replace('/&(?!(?:#x?[0-9a-f]+|[a-z]+);)/i', '&amp;', $content);
	
		if (!$doc->loadXML($content)) {
			KalturaLog::err('Failed to load XML');
			return $content;
		}
	
		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace('ns', 'http://www.w3.org/ns/ttml');
		
		$bodyElement = $xpath->query('//ns:body')->item(0);
		if ($bodyElement instanceof DOMElement) {
			$bodyElement->setAttribute('timeContainer', 'par');
		}
	
		$pElements = $xpath->query('//ns:p');
		$totalDuration = 0;
		foreach ($pElements as $pElement) {
			if ($pElement instanceof DOMElement) {
				$pElement->setAttribute('region', 'default');
	
				// add "end" attribute
				$beginValue = trim($pElement->getAttribute('begin'));
				$durationValue = trim($pElement->getAttribute('dur'));
				$endValue = trim($pElement->getAttribute('end'));
				$beginTime = kXml::timeToInteger($beginValue);
				$endTime = kXml::timeToInteger($endValue);
				// reformat "begin" attribute, transforms "00:00:29.73" to "00:00:29.730"
				if ($beginValue) {
					$pElement->removeAttribute('begin'); // remove to change order of attributes
					$pElement->setAttribute('begin', $this->integerToTime($beginTime));
				}
				// reformat "end" attribute, transforms "00:01:55.7" to "00:01:55.700"
				if ($endValue) {
					$pElement->removeAttribute('end'); // remove to change order of attributes
					$pElement->setAttribute('end', $this->integerToTime($endTime));
				}
				if ($beginValue && $endValue && !$durationValue) {
					$duration = $endTime - $beginTime;
					$pElement->setAttribute('dur', $this->formatDuration($duration));
					$totalDuration += $duration;
				}
			}
		}
	
		$divElement = $xpath->query('//ns:div')->item(0);
		if ($divElement instanceof DOMElement) {
			$divElement->setAttribute('begin', '00:00:00.000');
			$divElement->setAttribute('dur', $this->formatDuration($totalDuration));
			$divElement->setAttribute('timeContainer', 'seq');
		}
	
		return $doc->saveXML();
	}
	
	/*
	 * returns argument in miliseconds, three digits after decimal point
	 * 
	 *	@param $int
	 *	@return string
	 */
	private function formatDuration($int)
	{
		$durationSeconds = floor($int / 1000);
		$durationMilliseconds = $int % 1000;
		return $durationSeconds . '.' . str_pad($durationMilliseconds, 3, 0, STR_PAD_LEFT);
	}
	
	/**
	 * Wraps kXml::integerToTime to force milliseconds and pad it
	 *
	 * @param $int
	 * @return string
	 */
  	private function integerToTime($int)
  	{
		$milliseconds = $int % 1000;
		$stringTime = kXml::integerToTime($int - $milliseconds);
		$stringTime .= '.' . str_pad($milliseconds, 3, 0, STR_PAD_LEFT);
		return $stringTime;
	}
}
