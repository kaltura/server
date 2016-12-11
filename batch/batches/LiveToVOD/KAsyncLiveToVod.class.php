<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will copy objects and add them
 * according to the suppolied engine type and filter 
 *
 * @package Scheduler
 * @subpackage Copy
 */
class KAsyncLiveToVod extends KJobHandlerWorker
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 100;
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::LIVE_TO_VOD;
	}
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::LIVE_TO_VOD;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->copyCuePoint($job, $job->data);
	}
	
	/**
	 * Will take a data and copy cue points
	 */
	private function copyCuePoint(KalturaBatchJob $job, KalturaLiveToVodJobData $data)
	{
		$amfArray = json_decode($data->amfArray);
		$currentSegmentStartTime = self::getSegmentStartTime($amfArray);
		$currentSegmentEndTime = self::getSegmentEndTime($amfArray, $data->lastSegmentDuration);
		self::normalizeAMFTimes($amfArray, $data->totalVODDuration, $data->lastSegmentDuration);

		$totalCount = self::getCuePointCount($data->liveEntryId, $currentSegmentEndTime);
		KalturaLog::info("Total count of cue-point to copy: " .$totalCount);
		if ($totalCount == 0)
			return $this->closeJob($job, null, null, "No cue point to copy", KalturaBatchJobStatus::FINISHED);

		do
		{
			$copiedCuePointIds = array();
			$liveCuePointsToCopy = self::getCuePointlistForEntry($data->liveEntryId, $currentSegmentEndTime);
			if (count($liveCuePointsToCopy) == 0)
				break;

			//set the parnter ID for adding the new cue points in multi request
			KBatchBase::impersonate($liveCuePointsToCopy[0]->partnerId);
			KBatchBase::$kClient->startMultiRequest();
			foreach ($liveCuePointsToCopy as $liveCuePoint)
			{
				$copiedCuePointId = self::copyCuePointToVOD($liveCuePoint, $currentSegmentStartTime, $amfArray, $data->vodEntryId);
				if ($copiedCuePointId)
					$copiedCuePointIds[] = $copiedCuePointId;
			}
			$response = KBatchBase::$kClient->doMultiRequest();
			self::checkForErrorInMultiRequestResponse($response);
			KBatchBase::unimpersonate();
			
			//start post-process for all copied cue-point
			KalturaLog::info("Copied [".count($copiedCuePointIds)."] cue-points");
			self::postProcessCuePoints($copiedCuePointIds);

			//decrease the totalCount (as the number of cue point return from server)
			$totalCount -= count($liveCuePointsToCopy);
		} while ($totalCount);

		return $this->closeJob($job, null, null, "Copy all cue points finished", KalturaBatchJobStatus::FINISHED);
	}


	private static function checkForErrorInMultiRequestResponse($response)
	{
		$items = $response->result;
		foreach ($items as $item)
			KBatchBase::$kClient->throwExceptionIfError($item);
	}

	private static function postProcessCuePoints($copiedCuePointIds)
	{
		KBatchBase::$kClient->startMultiRequest();
		foreach ($copiedCuePointIds as $copiedLiveCuePointId)
			KBatchBase::$kClient->cuePoint->updateStatus($copiedLiveCuePointId, KalturaCuePointStatus::HANDLED);
		$response = KBatchBase::$kClient->doMultiRequest();
		self::checkForErrorInMultiRequestResponse($response);
	}

	private static function getCuePointFilter($entryId, $currentSegmentEndTime)
	{
		$filter = new KalturaCuePointFilter();
		$filter->entryIdEqual = $entryId;
		$filter->statusIn = CuePointStatus::READY;
		$filter->cuePointTypeIn = 'codeCuePoint.Code,thumbCuePoint.Thumb';
		$filter->createdAtLessThanOrEqual = $currentSegmentEndTime;
		return $filter;
	}

	private static function getCuePointCount($entryId, $currentSegmentEndTime)
	{
		$filter = self::getCuePointFilter($entryId, $currentSegmentEndTime);
		return KBatchBase::$kClient->cuePoint->count($filter);
	}

	private static function getCuePointlistForEntry($entryId, $currentSegmentEndTime)
	{
		$filter = self::getCuePointFilter($entryId, $currentSegmentEndTime);
		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_CUE_POINTS_TO_COPY_TO_VOD;
		$result = KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
		return $result->objects;
	}

	private static function getSegmentStartTime($amfArray)
	{
		if (count($amfArray) == 0)
		{
			KalturaLog::warning("getSegmentStartTime got an empty AMFs array - returning 0 as segment start time");
			return 0;
		}
		return ($amfArray[0]->ts - $amfArray[0]->pts) / 1000;
	}

	private static function getSegmentEndTime($amfArray, $segmentDuration)
	{
		return ((self::getSegmentStartTime($amfArray) * 1000) + $segmentDuration) / 1000;
	}
	// change the PTS of every amf to be relative to the beginning of the recording, and not to the beginning of the segment
	private static function normalizeAMFTimes(&$amfArray, $totalVODDuration, $currentSegmentDuration)
	{
		foreach($amfArray as $key=>$amf)
			$amfArray[$key]->pts = $amfArray[$key]->pts  + $totalVODDuration - $currentSegmentDuration;
	}

	private static function getOffsetForTimestamp($timestamp, $amfArray)
	{
		$minDistanceAmf = self::getClosestAMF($timestamp, $amfArray);
		$ret = 0;
		if (is_null($minDistanceAmf))
			KalturaLog::debug('minDistanceAmf is null - returning 0');
		elseif ($minDistanceAmf->ts > $timestamp)
			$ret = $minDistanceAmf->pts - ($minDistanceAmf->ts - $timestamp);
		else
			$ret = $minDistanceAmf->pts + ($timestamp - $minDistanceAmf->ts);
		// make sure we don't get a negative time
		$ret = max($ret,0);
		KalturaLog::debug('AMFs array is:' . print_r($amfArray, true) . 'getOffsetForTimestamp returning ' . $ret);
		return $ret;
	}

	private static function getClosestAMF($timestamp, $amfArray)
	{
		$len = count($amfArray);
		$ret = null;
		if ($len == 1)
			$ret = $amfArray[0];
		else if ($timestamp >= $amfArray[$len-1]->ts)
			$ret = $amfArray[$len-1];
		else if ($timestamp <= $amfArray[0]->ts)
			$ret = $amfArray[0];
		else if ($len > 1)
		{
			$lo = 0;
			$hi = $len - 1;
			while ($hi - $lo > 1)
			{
				$mid = round(($lo + $hi) / 2);
				if ($amfArray[$mid]->ts <= $timestamp)
					$lo = $mid;
				else
					$hi = $mid;
			}
			if (abs($amfArray[$hi]->ts - $timestamp) > abs($amfArray[$lo]->ts - $timestamp))
				$ret = $amfArray[$lo];
			else
				$ret = $amfArray[$hi];
		}
		KalturaLog::debug('getClosestAMF returning ' . print_r($ret, true));
		return $ret;
	}

	private static function cloneBaseCuePoint(KalturaCuePoint $src, KalturaCuePoint $dst)
	{
		$dst->partnerData = $src->partnerData;
		$dst->partnerSortValue = $src->partnerSortValue;
		$dst->thumbOffset = $src->thumbOffset;
		$dst->systemName = $src->systemName;
		$dst->tags = $src->tags;
		return $dst;
	}

	private static function cloneCuePoint(KalturaCuePoint $cuePoint) {
		switch($cuePoint->cuePointType) {
			case "codeCuePoint.Code":
				$newCuePoint = new KalturaCodeCuePoint();
				$newCuePoint->code = $cuePoint->code;
				break;
			case "thumbCuePoint.Thumb":
				$newCuePoint = new KalturaThumbCuePoint();
				break;
			default:
				return null;
		}
		//clone all default fields
		$newCuePoint = self::cloneBaseCuePoint($cuePoint,$newCuePoint);
		return $newCuePoint;
	}

	private static function copyCuePointFromLiveToVod($liveCuePoint, $startTime, $vodEntryId){
		$newCuePoint = self::cloneCuePoint($liveCuePoint);
		if (!$newCuePoint)
			return null;
		$newCuePoint->entryId = $vodEntryId;
		$newCuePoint->startTime = $startTime;
		return $newCuePoint;

	}
	private static function copyCuePointToVOD($liveCuePoint, $currentSegmentStartTime, $amfArray, $vodEntryId)
	{
		$cuePointCreationTime = $liveCuePoint->createdAt * 1000;
		// if the cp was before the segment start time - move it to the beginning of the segment.
		$cuePointCreationTime = max($cuePointCreationTime, $currentSegmentStartTime * 1000);

		$startTimeForCuePoint = self::getOffsetForTimestamp($cuePointCreationTime, $amfArray);
		if (!is_null($startTimeForCuePoint)) {
			$VODCuePoint = self::copyCuePointFromLiveToVod($liveCuePoint, $startTimeForCuePoint, $vodEntryId);
			if ($VODCuePoint)
			{
				KBatchBase::$kClient->cuePoint->add($VODCuePoint);
				return $liveCuePoint->id;
			}
		}
		else KalturaLog::info("Not copying cue point [$liveCuePoint->id]");
		return null;
	}
}
