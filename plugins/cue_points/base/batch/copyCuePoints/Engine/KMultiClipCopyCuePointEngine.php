<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KMultiClipCopyCuePointEngine extends KCopyCuePointEngine
{
	/** @var KalturaClipDescription $currentClip */
	private $currentClip = null;

	const CUE_POINT_THUMB = 'thumbCuePoint.Thumb';
	const CUE_POINT_EVENT = 'eventCuePoint.Event';
	const ANNOTATION = 'annotation.Annotation';
	const CUE_POINT_AD = 'adCuePoint.Ad';
	const CUE_POINT_CODE = 'codeCuePoint.Code';


	/**
	 * @return bool
	 * @throws Exception
	 */
	public function copyCuePoints()
	{
		$res = true;
		/** @var KalturaClipDescription $clipDescription */
		foreach ($this->data->clipsDescriptionArray as $clipDescription)
		{
			$this->currentClip = $clipDescription;
			$res &= $this->copyCuePointsToEntry($clipDescription->sourceEntryId, $this->data->destinationEntryId);
		}
		$this->mergeCuePoint($this->data->destinationEntryId);
		return $res;
	}

	/**
	 * @param KalturaCuePoint $cuePoint
	 * @return bool
	 */
	public function shouldCopyCuePoint($cuePoint)
	{
		$clipStartTime = $this->currentClip->startTime;
		$clipEndTime = $clipStartTime + $this->currentClip->duration;
		$calculatedEndTime = $cuePoint->calculatedEndTime;
		if ($cuePoint->isMomentary)
			$calculatedEndTime = $cuePoint->startTime;
		return is_null($calculatedEndTime) || TimeOffsetUtils::onTimeRange($cuePoint->startTime,$calculatedEndTime,$clipStartTime, $clipEndTime);
	}

	public function getCuePointFilter($entryId, $status = CuePointStatus::READY)
	{
		$filter = parent::getCuePointFilter($entryId, $status);
		$filter->startTimeLessThanOrEqual = $this->currentClip->startTime + $this->currentClip->duration;
		return $filter;
	}

	/**
	 * @param $cuePoint
	 * @return array
	 */
	public function calculateCuePointTimes($cuePoint)
	{
		$clipStartTime = $this->currentClip->startTime;
		$offsetInDestination = $this->currentClip->offsetInDestination;
		$clipEndTime = $clipStartTime + $this->currentClip->duration;
		$cuePointDestStartTime = TimeOffsetUtils::getAdjustedStartTime($cuePoint->startTime, $clipStartTime, $offsetInDestination);
		$cuePointDestEndTime = TimeOffsetUtils::getAdjustedEndTime(self::getCalculatedEndTimeIfExist($cuePoint), $clipStartTime ,$clipEndTime ,$offsetInDestination);
		return array($cuePointDestStartTime, $cuePointDestEndTime);
	}

	public function validateJobData()
	{
		if (!$this->data || !($this->data instanceof KalturaMultiClipCopyCuePointsJobData))
			return false;
		if (!$this->data->clipsDescriptionArray)
			return false;
		return parent::validateJobData();
	}

	/**
	 * @param string $destinationEntryId
	 * @throws Exception
	 */
	private function mergeCuePoint($destinationEntryId)
	{
		$filter = $this->getCuePointFilterForMerge($destinationEntryId);
		$pager = $this->getCuePointPager();
		$cuePoints = $this->getAllCuePointFromNewEntry($filter, $pager);
		$this->mergeCuePointByType($cuePoints);
	}

	private function getCuePointFilterForMerge($destinationEntryId)
	{
		$filter = parent::getCuePointFilter($destinationEntryId);
		//merge annotation,Ad,event,thumb cue point only
		$filter->cuePointTypeIn = self::CUE_POINT_THUMB . ',' . self::CUE_POINT_EVENT . ',' .self::ANNOTATION . ',' .
			self::CUE_POINT_AD  . ',' . self::CUE_POINT_CODE;
		return $filter;
	}

	/**
	 * @param $cuePoints
	 * @return array
	 * @throws Exception
	 */
	private function mergeCuePointByType($cuePoints)
	{
		$cuePointSplitIntoType = array(self::CUE_POINT_THUMB => array(), self::CUE_POINT_EVENT => array(),
			self::ANNOTATION => array(), self::CUE_POINT_AD => array(), self::CUE_POINT_CODE => array());
		/** @var KalturaCuePoint $cuePoint */
		foreach ($cuePoints as $cuePoint)
		{
			$this->handleNextCuePoint($cuePointSplitIntoType,$cuePoint);
		}
		return $cuePointSplitIntoType;
	}

	/**
	 * @param $filter
	 * @param $pager
	 * @return array
	 */
	private function getAllCuePointFromNewEntry($filter, $pager)
	{
		$cuePoints = array();
		do {
			$listResponse = KBatchBase::tryExecuteApiCall(array('KCopyCuePointEngine', 'cuePointList'), array($filter, $pager));
			if (!$listResponse)
				break;
			$cuePointsPage = $listResponse->objects;
			$pager->pageIndex++;
			$cuePoints = array_merge($cuePoints, $cuePointsPage);
		} while (count($cuePointsPage) == self::MAX_CUE_POINT_CHUNKS);
		return $cuePoints;
	}

	/**
	 * @param array $cuePointSplitIntoType
	 * @param KalturaCuePoint $cuePoint
	 */
	private function handleNextCuePoint(&$cuePointSplitIntoType, &$cuePoint)
	{
		/** @noinspection PhpIllegalArrayKeyTypeInspection */
		$type = &$cuePointSplitIntoType[$cuePoint->cuePointType];
		if (!key_exists($cuePoint->copiedFrom,$type))
			$type[$cuePoint->copiedFrom] = array();
		$copiedFromArray = $type[$cuePoint->copiedFrom];
		if ($copiedFromArray) // not Empty
		{
			$lastOfType = &$copiedFromArray[count($copiedFromArray)-1];
			if (!property_exists($lastOfType, 'endTime') || $lastOfType->endTime >= $cuePoint->startTime)
			{
				KBatchBase::tryExecuteApiCall(array('KCopyCuePointEngine', 'updateTimesAndDeleteNextCuePoint'),
					array($lastOfType, $cuePoint));
				if (property_exists($cuePoint, 'endTime'))
					$lastOfType->endTime = $cuePoint->endTime;
			}
			else //add new element to end of list
				$type[$cuePoint->copiedFrom][] = $cuePoint;
		}
		else // Empty(add new element)
			$type[$cuePoint->copiedFrom][] = $cuePoint;
	}

}
