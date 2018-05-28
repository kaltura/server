<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KMultiClipCopyCuePointEngine extends KCopyCuePointEngine
{
	/** @var KalturaClipDescription $currentClip */
    private $currentClip = null;

    public function copyCuePoints()
    {
        $res = true;
        /** @var KalturaClipDescription $clipDescription */
	    foreach ($this->data->clipsDescriptionArray as $clipDescription)
	    {
            $this->currentClip = $clipDescription;
            $res &= $this->copyCuePointsToEntry($clipDescription->sourceEntryId, $this->data->destinationEntryId);
        }
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
        if (is_null($cuePoint->calculatedEndTime) && $cuePoint->isMomentary)
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
        $cuePointDestEndTime = TimeOffsetUtils::getAdjustedEndTime($cuePoint->endTime, $clipStartTime ,$clipEndTime ,$offsetInDestination);
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
}