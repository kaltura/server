<?php
/**
 * Will extract the data of a single file 
 *
 * @package Scheduler
 * @subpackage ExtractData
 */
class KAsyncExtractData extends KJobHandlerWorker
{
	CONST SUB_TYPE_FIELD = 'subType';
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EXTRACT_DATA;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->extractData($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	/**
	 * Will take a single KalturaBatchJob and extract the media info for the given file
	 */
	private function extractData(KalturaBatchJob $job, KalturaExtractDataJobData $data)
	{
		$dataList = array();
		$engines = explode(",", $data->enginesType);
		$extraParams = array(KDataExtractEngine::ENTRY_ID_FIELD => $data->entryId,
							KDataExtractEngine::PARTNER_ID_FIELD =>$job->partnerId);
		foreach($engines as $engineType)
		{
			$engine = KDataExtractEngine::getInstance($engineType);
			if (!$engine)
			{
				KalturaLog::log("Engine type of [$engineType] not found");
				continue;
			}
			/**@var $engine KDataExtractEngine */
			$metadataArray = $engine->extractData($data->fileContainer, $extraParams);
			foreach($metadataArray as &$newData)
				$newData[self::SUB_TYPE_FIELD] = $engine->getSubType();
			$dataList = array_merge($dataList, $metadataArray);
		}

		// for all data add cue point
		KalturaLog::debug("Having list of data with: " . print_r($dataList, true));
		self::createCuePoint($job->partnerId, $data->entryId, $dataList);
		
		$this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		return $job;
	}
	

	private static function createCuePoint($partnerId, $entryId, $dataList)
	{
		KalturaLog::log("Creating " . count($dataList) . " cue points for entryId [$entryId]");
		KBatchBase::impersonate($partnerId);
		KBatchBase::$kClient->startMultiRequest();
		foreach ($dataList as $event) {
			$eventCuePoint = new KalturaEventCuePoint();
			$eventCuePoint->entryId = $entryId;
			$eventCuePoint->eventType = $event[self::SUB_TYPE_FIELD];
			$eventCuePoint->startTime = $event[KDataExtractEngine::START_TIME_FIELD];
			$eventCuePoint->data = $event[KDataExtractEngine::DATA_FIELD];
			KalturaLog::debug("sending cue point with as: " . print_r($eventCuePoint, true));
			KBatchBase::$kClient->cuePoint->add( $eventCuePoint ) ;
		}
		KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();
	}
	
	

}

