<?php
/**
 * @package plugins.visualRecognition
 * @subpackage Scheduler
 */
class KVisualRecognitionEngine implements KIntegrationCloserEngine
{
	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::dispatch()
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		KalturaLog::info("BUGA ".__FUNCTION__." dispatching Recognotion");
		return $this->doDispatch($job, $data, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		KalturaLog::info("BUGA ".__FUNCTION__." visual Recognotion");

		return $this->doClose($job, $data, $data->providerData);
	}

	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
		KalturaLog::info("BUGA " . __FUNCTION__ . " dispatching Recognotion");

		//KalturaLog::crit("Thumbnail interval [$providerData->thumbInterval]");
		if (!empty($job->entryId)) {
			KBatchBase::impersonate($job->partnerId);
			$entry = KBatchBase::$kClient->baseEntry->get($job->entryId);
			KBatchBase::unimpersonate();
			if (!($entry instanceof KalturaMediaEntry))
				throw new Exception("Invalid data type expected media");
		}

		$tumbnailsURLs = BaseDetectionEngine::getThumbnailUrls($entry->thumbnailUrl, $entry->duration, $providerData->thumbInterval);
		$cloudEngine = new CloudsapiDetectionEngine();
		$cloudEngine->init();
//		$clarifaiEngine = new ClarifaiDetectionEngine();
//		$clarifaiEngine->init();

		$externalJobs = array();

		foreach ($tumbnailsURLs as $thumbnailUrl)
		{
			KalturaLog::info("BUGA " . __FUNCTION__ . " thumbnail url = ".$thumbnailUrl);
			$returnValue = $cloudEngine->initiateRecognition($thumbnailUrl);
			if ($returnValue){
				$val = new KalturaKeyValue();
				$val->key = $thumbnailUrl;
				$val->value = $returnValue;
				$externalJobs[] = $val;
			}
			KalturaLog::info("BUGA " . __FUNCTION__ . " return value  = ".$returnValue);
			//$remoteJobIds[] = $returnValue;
//			$clarifaiEngine->initiateRecognition($thumbnailUrl);

		}

		$data->providerData->externalJobs = $externalJobs;
		KalturaLog::crit(print_r($data, true));
		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception


		// suppose here we call the nudity detector, and we call the function that says whether the entry is inappropriate or not, with the result and poviderData config we can do:
                if(WhateverClassNameNudityDetector->isInappropriate())
                {
	                KBatchBase::impersonate($job->partnerId);
	                switch($providerData->adultContentPolicy)
	                {
		                case KalturaVisualRecognitionAdultContentPolicy::AUTO_REJECT:
			                KBatchBase::$kClient->baseEntry->reject($job->entryId);
			                break;
		                case KalturaVisualRecognitionAdultContentPolicy::AUTO_FLAG:
			                $flag = new KalturaModerationFlag();
			                $flag->flaggedEntryId = $job->entryId;
			                $flag->flagType = KalturaModerationFlagType::SEXUAL_CONTENT'
			                KBatchBase::$kClient->baseEntry->flag($flag);
			                break;
		                case KalturaVisualRecognitionAdultContentPolicy::IGNORE:
		                default:
			                // do nothing
			                break;
                	}
                }

		return false;
	}

	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
		KalturaLog::info("BUGA ".__FUNCTION__." Thumbnail interval [$providerData->thumbInterval]");

		$cloudEngine = new CloudsapiDetectionEngine();
		$cloudEngine->init();

		// To finish, return true
		// To keep open for future closer, return false
		// To fail, throw exception
		$externalJobs = array();
		foreach($data->providerData->externalJobs as $thumb=>$externalJob)
		{
			KalturaLog::info("BUGA ".__FUNCTION__." checking job ".$externalJob);
			$farresult = $cloudEngine->checkRecognitionStatus($externalJob);
			if ($farresult !== false )
			{
				KalturaLog::info("SUSU ".print_r($farresult, true));

			} else {
				$externalJobs[$thumb] = $externalJob;
			}
		}
		$data->providerData->externalJobs = $externalJobs;
		return empty($externalJobs);
	}
}
