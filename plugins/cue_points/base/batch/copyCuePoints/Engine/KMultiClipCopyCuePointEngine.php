<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KMultiClipCopyCuePointEngine extends KCopyCuePointEngine
{
	/** @var KalturaClipDescription $currentClip */
	private $currentClip = null;

	/**
	 * @return bool
	 * @throws KalturaAPIException
	 */
	const CUE_POINT_THUMB = 'thumbCuePoint.Thumb';

	const CUE_POINT_EVENT = 'eventCuePoint.Event';

	const ANNOTATION = 'annotation.Annotation';

	const CUE_POINT_AD = 'adCuePoint.Ad';

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
		$cuePointDestEndTime = TimeOffsetUtils::getAdjustedEndTime(self::getEndTimeIfExist($cuePoint), $clipStartTime ,$clipEndTime ,$offsetInDestination);
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

	public function setData($data, $partnerId)
	{
		parent::setData($data,$partnerId);
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
		$cuePoints = array();
		$cuePointSplitIntoType = array(self::CUE_POINT_THUMB => array(), self::CUE_POINT_EVENT => array(),
				self::ANNOTATION =>array(), self::CUE_POINT_AD =>array());
		$this->getAllCuePointFromNewEntry($filter, $pager, $cuePoints);
		$this->sortCuePointIntoType($cuePoints, $cuePointSplitIntoType);
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
				/** @var KalturaCuePoint $nextCuePoint */
				$nextCuePoint = &$type[$i + 1];
				if ($currentCuePoint->copiedFrom === $nextCuePoint->copiedFrom) {
					$currentCuePointEndTimeExist = property_exists($currentCuePoint, 'endTime');
					if ($currentCuePointEndTimeExist && $currentCuePoint->endTime != $nextCuePoint->startTime)
						continue;
					$res = KBatchBase::tryExecuteApiCall(array('KCopyCuePointEngine', 'updateTimesAndDeleteNextCuePoint'), array($currentCuePoint, $nextCuePoint));
					if ($res)
					{
						if ($currentCuePointEndTimeExist)
							$currentCuePoint->endTime = $nextCuePoint->endTime;
						unset($type[$i + 1]);
						$type = array_values($type);
						$i--; //keep on same index check if need to merge next as well
					}
				}
			}
		}
	}

	/**
	 * @param $cuePoints
	 * @param $cuePointSplitIntoType
	 * @throws Exception
	 */
	private function sortCuePointIntoType($cuePoints, &$cuePointSplitIntoType)
	{
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
	}

	/**
	 * @param $filter
	 * @param $pager
	 * @param $cuePoints
	 */
	private function getAllCuePointFromNewEntry($filter, $pager, &$cuePoints)
	{
		do {
			$listResponse = KBatchBase::tryExecuteApiCall(array('KCopyCuePointEngine', 'cuePointList'), array($filter, $pager));
			if (!$listResponse)
				continue;
			$cuePointsPage = $listResponse->objects;
			$pager->pageIndex++;
			$cuePoints = array_merge($cuePoints, $cuePointsPage);
		} while (count($cuePointsPage) == self::MAX_CUE_POINT_CHUNKS);
	}


}
