<?php
/**
 * @package Scheduler
 * @subpackage copyCuePoints
 */

/**
 * Clear cue points from live entries that were not marked as handled (cases were recording is off)
 *
 * @package Scheduler
 * @subpackage copyCuePointsConcat
 */
class KAsyncCopyCuePoints extends KJobHandlerWorker
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 100;

	/*
	 * (non-PHPdoc)
	 *  @see KBatchBase::getJobType();
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY_CUE_POINTS;
	}

	/*
	 * (non-PHPdoc)
	 *  @see KBatchBase::getJobType();
	 */
	public static function getJobType()
	{
		return KalturaBatchJobType::COPY_CUE_POINTS;
	}


	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		/** @var KalturaCopyCuePointsJobData $data */
		$data = $job->data;
		if (!$data->clipsDescriptionArray)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP,
				KalturaBatchJobAppErrors::MISSING_PARAMETERS,
				'Job Has No ArrayData', KalturaBatchJobStatus::FAILED);
		}
		return $this->copyCuePoint($job, $data);
	}

	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaCopyCuePointsJobData $data
	 * @return KalturaBatchJob
	 */
	private function copyCuePoint(KalturaBatchJob $job, KalturaCopyCuePointsJobData $data)
	{
		self::impersonate($job->partnerId);
		$currentDuration = 0;
		$totalCuePointNumber = 0;
		/** @var KalturaCopyCuePointsJobData $data */
		/** @var KalturaClipDescription $clipDescription */
		foreach ($data->clipsDescriptionArray as $clipDescription)
		{
			$sourceEntryId = $clipDescription->sourceEntryId;
			$clipStartTime = $clipDescription->startTime;
			$clipEndTime = $clipStartTime + $clipDescription->duration;
			$cuePointCount = $this->getCuePointCount($sourceEntryId,$clipStartTime,$clipEndTime);
			$totalCuePointNumber += $cuePointCount;
			if ($cuePointCount == 0)
			{
				KalturaLog::info("clip ID: "."$clipDescription->sourceEntryId " . "has no cue point between 
								 $clipStartTime and $clipEndTime");
				$currentDuration = $currentDuration + $clipDescription->duration;
				continue;
			}
			KalturaLog::info("Total count of cue-point to copy: " .$cuePointCount);
			$cuePointList = $this->getCuePointListForEntry($sourceEntryId,$clipStartTime,$clipEndTime);
			$this->copyToDestEntry($data->destinationEntryId, $clipStartTime, $currentDuration, $cuePointList);
			$currentDuration = $currentDuration + $clipDescription->duration;
		}
		self::unimpersonate();
		return $this->closeJob($job, null, null,
			"All Cue Point Copied ,total number of cue point $totalCuePointNumber",
			KalturaBatchJobStatus::FINISHED);

	}


	/**
	 * @param $entryId
	 * @param $currentSegmentEndTime
	 * @param $endTime
	 * @return int count
	 */
	private function getCuePointCount($entryId, $currentSegmentEndTime, $endTime)
	{
		$filter = self::getCuePointFilter($entryId, $currentSegmentEndTime, $endTime);
		/** @noinspection PhpUndefinedFieldInspection */
		return KBatchBase::$kClient->cuePoint->count($filter);
	}

	/**
	 * @param $entryId
	 * @param $startTime
	 * @param $endTime
	 * @return KalturaCuePoint[]
	 */
	private function getCuePointListForEntry($entryId, $startTime, $endTime)
	{
		$filter = self::getCuePointFilter($entryId, $startTime, $endTime);
		/** @noinspection PhpUndefinedClassInspection */
		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_CUE_POINTS_TO_COPY_TO_VOD;
		/** @noinspection PhpUndefinedFieldInspection */
		$result = KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
		return $result->objects;
	}


	private function getCuePointFilter($entryId, $currentClipStartTime ,$currentClipEndTime)
	{
		$filter = new KalturaCuePointFilter();
		$filter->entryIdEqual = $entryId;
		$filter->statusIn = CuePointStatus::READY;
		$filter->startTimeGreaterThanOrEqual = $currentClipStartTime;
		$filter->startTimeLessThanOrEqual = $currentClipEndTime;
		return $filter;
	}

	/**
	 * @param $destinationEntryId
	 * @param $clipStartTime
	 * @param $currentDuration
	 * @param $cuePointList
	 */
	private function copyToDestEntry($destinationEntryId, $clipStartTime, $currentDuration, $cuePointList)
	{
		/** @var KalturaCuePoint $cuePoint */
		foreach ($cuePointList as $cuePoint)
		{
			$cuePointDestStartTime = $cuePoint->startTime - $clipStartTime + $currentDuration;
			/** @noinspection PhpUndefinedFieldInspection */
			$clonedCuePoint = KBatchBase::$kClient->cuePoint->cloneAction($cuePoint->id, $destinationEntryId);
			if (KBatchBase::$kClient->isError($clonedCuePoint))
			{
				KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");
			}
			if ($clonedCuePoint) {
				/** @noinspection PhpUndefinedFieldInspection */
				$res = $this->updateStartTime($clonedCuePoint, $cuePointDestStartTime);
				if (!KBatchBase::$kClient->isError($res))
				{
					KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");
				}

			}

		}
	}

	/**
	 * @param $clonedCuePoint
	 * @param $cuePointDestStartTime
	 * @return mixed
	 */
	private function updateStartTime($clonedCuePoint, $cuePointDestStartTime)
	{
		/** @noinspection PhpUndefinedFieldInspection */
		$res = KBatchBase::$kClient->cuePoint->updateStartTime($clonedCuePoint->id, $cuePointDestStartTime);
		return $res;
	}


}
