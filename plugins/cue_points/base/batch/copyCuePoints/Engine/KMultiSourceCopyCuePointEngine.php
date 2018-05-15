<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KMultiSourceCopyCuePointEngine extends KCopyCuePointEngine
{
    private $currentClip = null;

    public function copyCuePoints()
    {
        foreach ($this->data->clipsDescriptionArray as $clipDescription) {
            $this->currentClip = $clipDescription;
            $res = $this->copyCuePointsToEntry($clipDescription->sourceEntryId, $this->data->destinationEntryId);
        }
        return true;
    }

    public function shouldCopyCuePoint($cuePoint)
    {
        $clipStartTime = $this->currentClip->startTime;
        $clipEndTime = $clipStartTime + $this->currentClip->duration;
        return !is_null($cuePoint->endTime) && !TimeOffsetUtils::onTimeRange($cuePoint->startTime,$cuePoint->endTime,$clipStartTime, $clipEndTime);
    }

    public function getCuePointFilter($entryId, $status = CuePointStatus::READY)
    {
        $filter = parent::getCuePointFilter($entryId, $status);
        $filter->startTimeLessThanOrEqual = $this->currentClip->startTime + $this->currentClip->duration;
        return $filter;
    }

    public function calculateCuePointTimes($cuePoint)
    {

        $clipStartTime = $this->clipDescription->startTime;
        $offsetInDestination = $this->clipDescription->offsetInDestination;
        $clipEndTime = $clipStartTime + $this->clipDescription->duration;
        $cuePointDestStartTime = TimeOffsetUtils::getAdjustedStartTime($cuePoint->startTime, $clipStartTime, $offsetInDestination);
        $cuePointDestEndTime = null;
        if ($cuePoint->endTime)
            $cuePointDestEndTime = TimeOffsetUtils::getAdjustedEndTime($cuePoint->endTime, $clipStartTime ,$clipEndTime ,$offsetInDestination);
        return array($cuePointDestStartTime, $cuePointDestEndTime);
    }

    public function validateJobData() 
    {
        if (!$this->data || !$this->data->clipsDescriptionArray)
            return false;
        return parent::validateJobData();
    }
}