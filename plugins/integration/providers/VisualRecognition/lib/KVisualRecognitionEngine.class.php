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
		KalturaLog::info("BUGA " . __FUNCTION__ . " Dispatching Visual Recognition");

		if (!empty($job->entryId)) {
			KBatchBase::impersonate($job->partnerId);
			$entry = KBatchBase::$kClient->baseEntry->get($job->entryId);
			KBatchBase::unimpersonate();
			if (!($entry instanceof KalturaMediaEntry))
				throw new Exception("Invalid data type expected media");
		}

		$thumbnailURLs = BaseDetectionEngine::getThumbnailUrls($entry->thumbnailUrl, $entry->duration, $providerData->thumbInterval);
		// first run all the async detectors
		$cloudEngine = new CloudsapiDetectionEngine();
		$clarifaiEngine = new ClarifaiDetectionEngine();

		$externalJobs = array_merge(
			$this->runAsyncDetection($cloudEngine, $thumbnailURLs),
			$this->runAsyncDetection($clarifaiEngine, $thumbnailURLs)
			);

		$data->providerData->externalJobs = $externalJobs;

		// run the sync detectors
		$sightEngine = new SightDetectionEngine();
		$isThereNudity = $this->runSyncDetection($sightEngine, $thumbnailURLs);

		KalturaLog::info("BUGA Nudity status for found as : ".$isThereNudity);
		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception


		// suppose here we call the nudity detector, and we call the function that says whether the entry is inappropriate or not, with the result and poviderData config we can do:
                /*if(WhateverClassNameNudityDetector->isInappropriate())
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
			                $flag->flagType = KalturaModerationFlagType::SEXUAL_CONTENT;
			                KBatchBase::$kClient->baseEntry->flag($flag);
			                break;
		                case KalturaVisualRecognitionAdultContentPolicy::IGNORE:
		                default:
			                // do nothing
			                break;
                	}
                }*/

		return false;
	}

	private function runSyncDetection(IDetectionEngine $detectionEngine, $thumbnailURLs)
	{
		$detectionEngine->init();
		$returnValue = $detectionEngine->initiateRecognition($thumbnailURLs);
		return $returnValue;
	}

	private function runAsyncDetection(IDetectionEngine $detectionEngine, $thumbnailURLs)
	{
		$remoteIDs = array();
		$detectionEngine->init();

		$returnValues = $detectionEngine->initiateRecognition($thumbnailURLs);

		foreach ($returnValues as $sec=>$remoteId)
		{
			$val = new RemoteEntityData();
			$val->sec = $sec;
			$val->detectorType = get_class($detectionEngine);
			$val->remoteId = $remoteId;
			$remoteIDs[] = $val;
		}
		return $remoteIDs;
	}

	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
		KalturaLog::info("BUGA ".__FUNCTION__." Thumbnail interval [$providerData->thumbInterval]");

		$cloudEngine = new CloudsapiDetectionEngine();
		$cloudEngine->init();
		$clarifaiEngine = new ClarifaiDetectionEngine();
		$clarifaiEngine->init();

		// To finish, return true
		// To keep open for future closer, return false
		// To fail, throw exception
		$externalJobs = array();
		foreach($data->providerData->externalJobs as $remoteJob)
		{
			// we care only about the async detector - should be implemented with separate interface
			if ($remoteJob->detectorType == 'CloudsapiDetectionEngine')
				$detector = $cloudEngine;
			else if ($remoteJob->detectorType == 'ClarifaiDetectionEngine')
				$detector = $clarifaiEngine;
			else
				throw new Exception("Failed to find relevant Detection Engine for given detector type given : ".$remoteJob->detectorType);
			$detectorResult = $detector->checkRecognitionStatus($remoteJob->remoteId);
			if ($detectorResult !== false )	{
				KalturaLog::info("BUGA detection on remote detector succeeded ".print_r($detectorResult, true));
			} else {
				$externalJobs[] = $remoteJob;
			}
		}
		$data->providerData->externalJobs = $externalJobs;
		return empty($externalJobs);
	}


	public function createThumbCuePoint(array $thumbCuePointsInitData) {
		if (!empty($thumbCuePointsInitData)) {
			KBatchBase::$kClient->startMultiRequest();
			foreach ($thumbCuePointsInitData as $thumbCuePointInitData) {
				$cuePoint = new KalturaThumbCuePoint();
				$cuePoint->assetId = $thumbCuePointInitData->entryId;
				$cuePoint->description = $thumbCuePointInitData->data;
				$cuePoint->startTime = $thumbCuePointInitData->startTime;
				$cuePoint->subType = ThumbCuePointSubType::SLIDE;
				$cuePoint->tags = 'origin_visual_recognition';
				KBatchBase::$kClient->cuePoint->add($cuePoint);
			}
			KBatchBase::$kClient->doMultiRequest();
		}
	}
}

class RemoteEntityData {
	public $sec;
	public $detectorType;
	public $remoteId;
}
