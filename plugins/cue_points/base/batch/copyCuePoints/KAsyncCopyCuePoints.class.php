<?php
/**
 * @package Scheduler
 * @subpackage copyCuePoints
 */

class KAsyncCopyCuePoints extends KJobHandlerWorker
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 500;

	/*
	 * (non-PHPdoc)
	 *  @see KBatchBase::getJobType();
	 */
	const ATTEMPT_ALLOWED = 3;

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
		KalturaLog::info('Copy Cue Point Job Started');
		$totalCuePointNumber = 0;
		/** @var KalturaCopyCuePointsJobData $data */
		/** @var KalturaClipDescription $clipDescription */
		foreach ($data->clipsDescriptionArray as $clipDescription)
		{
			$sourceEntryId = $clipDescription->sourceEntryId;
			$clipStartTime = $clipDescription->startTime;
			$offsetInDestination = $clipDescription->offsetInDestination;
			$clipEndTime = $clipStartTime + $clipDescription->duration;
			$cuePointList = $this->getCuePointListForEntry($sourceEntryId, $clipEndTime);
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
	 * @param $endTime
	 * @return KalturaCuePoint[]
	 */
	private function getCuePointListForEntry($entryId, $endTime)
	{
		$filter = $this->getCuePointFilter($entryId, $endTime);
		/** @noinspection PhpUndefinedClassInspection */
		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_CUE_POINTS_TO_COPY_TO_VOD;
		/** @noinspection PhpUndefinedFieldInspection */
		$result = null;
		$result = $this->getCuePointList($filter, $pager);

		/** @noinspection PhpUndefinedFieldInspection */
		return $result->objects;
	}


	private function getCuePointFilter($entryId, $currentClipEndTime)
	{
		$filter = new KalturaCuePointFilter();
		$filter->entryIdEqual = $entryId;
		$filter->statusIn = CuePointStatus::READY;
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
			/** @noinspection PhpUndefinedFieldInspection */
			if (!is_null($cuePoint->endTime) && !TimeOffsetUtils::onTimeRange($cuePoint->startTime,$cuePoint->endTime,$clipStartTime, $clipEndTime))
				continue;
			$cuePointDestStartTime = TimeOffsetUtils::getAdjustedStartTime($cuePoint->startTime, $clipStartTime, $offsetInDestination);
			$cuePointDestEndTime = null;
			/** @noinspection PhpUndefinedFieldInspection */
			if (!is_null($cuePoint->endTime))
				/** @noinspection PhpUndefinedFieldInspection */
				$cuePointDestEndTime = TimeOffsetUtils::getAdjustedEndTime($cuePoint->endTime, $clipStartTime ,$clipEndTime ,$offsetInDestination);
			$clonedCuePoint = $this->cloneCuePoint($destinationEntryId, $cuePoint);
			if (KBatchBase::$kClient->isError($clonedCuePoint))
			{
				KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");
			}
			if ($clonedCuePoint) {
				/** @noinspection PhpUndefinedFieldInspection */
				$res = $this->updateCuePointTimes($clonedCuePoint, $cuePointDestStartTime,$cuePointDestEndTime);
				if (KBatchBase::$kClient->isError($res))
					KalturaLog::alert("Error during copy , of cuePoint $clonedCuePoint->id");

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
		$attempts = 0;
		$res = null;
		do {

			try {
				/** @noinspection PhpUndefinedFieldInspection */
				$res = KBatchBase::$kClient->cuePoint->updateCuePointsTimes($clonedCuePoint->id, $cuePointDestStartTime,$cuePointDestEndTime);
				break;
			} catch (Exception $ex) {
				$attempts++;
				KalturaLog::warning("API Call failed number of retires " . $attempts);
				KalturaLog::err($ex);
			}

		} while ($attempts < self::ATTEMPT_ALLOWED);
		/** @noinspection PhpUndefinedFieldInspection */

		return $res;
	}

	/**
	 * @param $filter
	 * @param $pager
	 * @return array
	 */
	private function getCuePointList($filter, $pager)
	{
		$attempts = 0;
		$result = null;
		do {

			try {
				/** @noinspection PhpUndefinedFieldInspection */
				$result = KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
				break;
			} catch (Exception $ex) {
				$attempts++;
				KalturaLog::warning("API Call failed number of retires " . $attempts);
				KalturaLog::err($ex);
			}

		} while ($attempts < self::ATTEMPT_ALLOWED);
		return $result;
	}

	/**
	 * @param $destinationEntryId
	 * @param $cuePoint
	 * @return mixed
	 */
	private function cloneCuePoint($destinationEntryId, $cuePoint)
	{
		$attempts = 0;
		$clonedCuePoint = null;
		do {

			try {
				/** @noinspection PhpUndefinedFieldInspection */
				$clonedCuePoint = KBatchBase::$kClient->cuePoint->cloneAction($cuePoint->id, $destinationEntryId);
				break;
			} catch (Exception $ex) {
				$attempts++;
				KalturaLog::warning("API Call failed number of retires " . $attempts);
				KalturaLog::err($ex);
			}

		} while ($attempts < self::ATTEMPT_ALLOWED);
		return $clonedCuePoint;
	}


}
