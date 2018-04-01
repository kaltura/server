<?php
/**
 * @package Scheduler
 * @subpackage copyCuePoints
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
		$totalCuePointNumber = 0;
		/** @var KalturaCopyCuePointsJobData $data */
		/** @var KalturaClipDescription $clipDescription */
		foreach ($data->clipsDescriptionArray as $clipDescription)
		{
			$sourceEntryId = $clipDescription->sourceEntryId;
			$clipStartTime = $clipDescription->startTime;
			$offsetInDestination = $clipDescription->offsetInDestination;
			$clipEndTime = $clipStartTime + $clipDescription->duration;
			$cuePointList = $this->getCuePointListForEntry($sourceEntryId,$clipStartTime,$clipEndTime);
			$count = count($cuePointList);
			$totalCuePointNumber += $count;
			if (!$count)
			{
				KalturaLog::info("clip ID: "."$clipDescription->sourceEntryId " . "has no cue point between 
								 $clipStartTime and $clipEndTime");
				continue;
			}
			KalturaLog::info("Total count of cue-point to copy: " .$count);

			$this->copyToDestEntry($data->destinationEntryId, $clipStartTime, $offsetInDestination, $cuePointList,$clipEndTime);
		}
		self::unimpersonate();
		return $this->closeJob($job, null, null,
			"All Cue Point Copied ,total number of cue point $totalCuePointNumber",
			KalturaBatchJobStatus::FINISHED);

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
	 * @param $offsetInDestination
	 * @param $clipEndTime
	 * @param $cuePointList
	 */
	private function copyToDestEntry($destinationEntryId, $clipStartTime, $offsetInDestination, $cuePointList, $clipEndTime)
	{
		/** @var KalturaCuePoint $cuePoint */
		foreach ($cuePointList as $cuePoint)
		{
			$cuePointDestStartTime = $cuePoint->startTime - $clipStartTime + $offsetInDestination;
			$cuePointDestEndTime = null;
			/** @noinspection PhpUndefinedFieldInspection */
			if (!is_null($cuePoint->endTime))
			{
				/** @noinspection PhpUndefinedFieldInspection */
				$cuePointDestEndTime = min($cuePoint->endTime - $clipStartTime + $offsetInDestination, $clipEndTime - $clipStartTime + $offsetInDestination);
			}
			/** @noinspection PhpUndefinedFieldInspection */
			$clonedCuePoint = KBatchBase::$kClient->cuePoint->cloneAction($cuePoint->id, $destinationEntryId);
			if (KBatchBase::$kClient->isError($clonedCuePoint))
			{
				KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");
			}
			if ($clonedCuePoint) {
				/** @noinspection PhpUndefinedFieldInspection */
				$res = $this->updateCuePointTimes($clonedCuePoint, $cuePointDestStartTime,$cuePointDestEndTime);
				if (KBatchBase::$kClient->isError($res))
				{
					KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");
				}

			}

		}
	}

	/**
	 * @param $clonedCuePoint
	 * @param $cuePointDestStartTime
	 * @param $cuePointDestEndTime
	 * @return mixed
	 */
	private function updateCuePointTimes($clonedCuePoint, $cuePointDestStartTime, $cuePointDestEndTime)
	{
		/** @noinspection PhpUndefinedFieldInspection */
		$res = KBatchBase::$kClient->cuePoint->updateCuePointsTimes($clonedCuePoint->id, $cuePointDestStartTime,$cuePointDestEndTime);
		return $res;
	}


}
