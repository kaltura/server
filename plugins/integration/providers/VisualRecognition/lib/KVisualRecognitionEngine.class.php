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
            {
				throw new Exception("Invalid data type expected media");
            }
		}

		$tumbnailsURLs = BaseDetectionEngine::getThumbnailUrls($entry->thumbnailUrl, $entry->duration, $providerData->thumbInterval);
		$cloudEngine = new CloudsapiDetectionEngine();
		$cloudEngine->init();
//		$clarifaiEngine = new ClarifaiDetectionEngine();
//		$clarifaiEngine->init();

		$jobs = array();
        if($cloudEngine->asyncCall())
        {
            KalturaLog::info("BUGA " . __FUNCTION__ . " thumbnail url = ".print_r($tumbnailsURLs, true));
            $jobs = $cloudEngine->initiateRecognition($tumbnailsURLs);
            KalturaLog::info("BUGA " . __FUNCTION__ . " return value  = ".print_r($jobs, true));
            $externalJobs = array();
            foreach($jobs as $sec => $token)
            {
                $keyVal = new KalturaKeyValue();
                $keyVal->key = $sec;
                $keyVal->value = $token;
                $externalJobs[] = $keyVal;
            }
            $data->providerData->externalJobs = $externalJobs;
        }

		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception

/*
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
 * 
 */

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
        
        $jobIds = array();
        foreach($providerData->externalJobs as $keyVal)
        {
            $jobIds[$keyVal->key] = $keyVal->value;
        }

        KalturaLog::info("BUGA ".__FUNCTION__." checking job ".$providerData->externalJobs);
        $results = $cloudEngine->checkRecognitionStatus($jobIds);
        if ($results === false )
        {
            // job not closed, wait for another closer
            KalturaLog::info("SUSU ".print_r($results, true));
            return false;
        } else {
            // return true to close the job
            
            // create cuepoints from array of results
            $this->createThumbCuePoint($job->partnerId, $results, $job->entryId);
            return true;
        }
	}
    
    public function createThumbCuePoint($partnerId, array $thumbCuePointsInitData, $entryId) {
        if (!empty($thumbCuePointsInitData)) {
            KBatchBase::impersonate($partnerId);
            KBatchBase::$kClient->startMultiRequest();
            foreach ($thumbCuePointsInitData as $sec => $thumbCuePointInitData) {
                $cuePoint = new KalturaThumbCuePoint();
                $cuePoint->entryId = $entryId;
                $cuePoint->description = implode(' ', $thumbCuePointInitData);
                $cuePoint->startTime = $sec*1000;
                $cuePoint->subType = ThumbCuePointSubType::SLIDE;
                $cuePoint->tags = 'origin_visual_recognition';
                KBatchBase::$kClient->cuePoint->add($cuePoint);
            }
            KBatchBase::$kClient->doMultiRequest();
            KBatchBase::unimpersonate();
        }
    }
}
