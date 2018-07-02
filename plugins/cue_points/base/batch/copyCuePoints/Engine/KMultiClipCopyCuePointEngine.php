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

	/**
	 * @return bool
	 * @throws KalturaAPIException
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
		if ($res)
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
	 * @throws KalturaAPIException
	 * @throws Exception
	 */
	private function mergeCuePoint($destinationEntryId)
	{

		$filter = $this->getCuePointFilterForMerge($destinationEntryId);
		$pager = $this->getCuePointPager();
		$cuePoints = $this->getAllCuePointFromNewEntry($filter, $pager);
		$cuePointSplitIntoType = $this->sortCuePointIntoType($cuePoints);
		$this->locateCorrespondingCuePointsAndMergeThem($cuePointSplitIntoType);
	}

	private function getCuePointFilterForMerge($destinationEntryId)
	{
		$filter = parent::getCuePointFilter($destinationEntryId);
		//merge annotation,Ad,event,thumb cue point only
		$filter->cuePointTypeIn = self::CUE_POINT_THUMB . ',' . self::CUE_POINT_EVENT . ',' .self::ANNOTATION . ',' .
			self::CUE_POINT_AD;
		return $filter;
	}

	/**
	 * @param $cuePointSplitIntoType
	 */
	private function locateCorrespondingCuePointsAndMergeThem($cuePointSplitIntoType)
	{
		/** @var array[KalturaCuePoint] $type */
		foreach ($cuePointSplitIntoType as $type) {
			for ($i = 0; $i < count($type) - 1; $i++) {
				/** @var KalturaCuePoint $currentCuePoint */
				$currentCuePoint = &$type[$i];
				//if Next element does not exit break the loop
				if(!array_key_exists($i + 1,$type))
					break;
				$relatedCuePointIndex = $this->getNextRelatedElementIndex($type,$i,$currentCuePoint);
				/** @var KalturaCuePoint $relatedCuePoint */
				$relatedCuePoint = null;
				if (array_key_exists($relatedCuePointIndex,$type))
					$relatedCuePoint =  &$type[$relatedCuePointIndex];
				if ($relatedCuePoint)
				{
					$currentCuePointEndTimeExist = property_exists($currentCuePoint, 'endTime');
					$res = KBatchBase::tryExecuteApiCall(array('KCopyCuePointEngine', 'updateTimesAndDeleteNextCuePoint'), array($currentCuePoint, $relatedCuePoint));
					if ($res)
					{
						if ($currentCuePointEndTimeExist)
							$currentCuePoint->endTime = $relatedCuePoint->endTime;
						unset($type[$relatedCuePointIndex]);
						$type = array_values($type);
						$i--; //keep on same index check if need to merge next as well
					}
				}
			}
		}
	}

	/**
	 * @param $cuePoints
	 * @return array
	 * @throws Exception
	 */
	private function sortCuePointIntoType($cuePoints)
	{
		$cuePointSplitIntoType = array(self::CUE_POINT_THUMB => array(), self::CUE_POINT_EVENT => array(),
															self::ANNOTATION => array(), self::CUE_POINT_AD => array());
		/** @var KalturaCuePoint $cuePoint */
		foreach ($cuePoints as $cuePoint) {
			switch ($cuePoint->cuePointType) {
				case self::CUE_POINT_THUMB:
					$cuePointSplitIntoType[self::CUE_POINT_THUMB][] = $cuePoint;
					break;
				case self::CUE_POINT_EVENT:
					$cuePointSplitIntoType[self::CUE_POINT_EVENT][] = $cuePoint;
					break;
				case self::ANNOTATION:
					$cuePointSplitIntoType[self::ANNOTATION][] = $cuePoint;
					break;
				case self::CUE_POINT_AD:
					$cuePointSplitIntoType[self::CUE_POINT_AD][] = $cuePoint;
					break;
				default:
					throw new KalturaAPIException("for cue point: $cuePoint->id , Type: $cuePoint->cuePointType , should not return to the merger.");
			}
		}
		return $cuePointSplitIntoType;
	}

	/**
	 * @param $filter
	 * @param $pager
	 * @param $cuePoints
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
	 * @param array $type
	 * @param int $i
	 * @param KalturaCuePoint $currentCuePoint
	 * @return int|null
	 */
	private function getNextRelatedElementIndex($type, $i, $currentCuePoint)
	{

		do {
			$i++;
			$candidate = $type[$i];
			$currentCuePointEndTimeExist = property_exists($currentCuePoint, 'endTime');
			if ($currentCuePointEndTimeExist && $currentCuePoint->endTime != $candidate->startTime)
				break;
			if ($currentCuePoint->copiedFrom === $candidate->copiedFrom)
				return $i;
		} while (array_key_exists($i + 1,$type));
		return -1;
	}


}
