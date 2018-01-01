<?php
/**
 * @package Scheduler
 * @subpackage Extract-Data
 */

/**
 * Will extract the data of a single file 
 *
 * @package Scheduler
 * @subpackage Extract-Data
 */
class KAsyncExtractData extends KJobHandlerWorker
{
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
		KalturaLog::debug("inside the new Job");
		KalturaLog::debug(print_r($data->enginesType, true));
		KalturaLog::debug(print_r($data->fileContainer, true));

		foreach($data->enginesType as $engineType)
		{
			$engine = self::getEngineByType($engineType);
			if (!$engine)
			{
				KalturaLog::log("Engine type of [$engineType] not found");
				continue;
			}
			$newData = $engine->getData($data->fileContainer);
			$dataList = array_merge($dataList, $newData);
		}

		// for all data add cue point
		self::createCuePoint($data->entryId, $dataList);
		
		$this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}

	private static function getEngineByType($engineType)
	{
		switch ($engineType)
		{
			case KalturaDataExtractEngineType::MUSIC_RECOGNIZER:
				return null;
			case KalturaDataExtractEngineType::CHAPTER_LINKER:
				return null;
			default:
				return null;
		}
	}

	private static function createCuePoint($entryId, $dataList)
	{
		KalturaLog::log("Creating " . count($dataList) . " cue points for entryId [$entryId]");
		KBatchBase::$kClient->startMultiRequest();
		foreach ($dataList as $data) {
			$eventCuePoint = new KalturaEventCuePoint();
			$eventCuePoint->entryId = $entryId;
			$eventCuePoint->eventType = $data[0];
			$eventCuePoint->triggeredAt = $data[1];
			$eventCuePoint->data = $data[2];
			
			KBatchBase::$kClient->cuePoint->add( $eventCuePoint ) ;
		}
		KBatchBase::$kClient->doMultiRequest();
	}
	
	

}

