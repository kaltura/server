<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KMultiClipCopyCuePointEngine extends KCopyCuePointEngine
{
    private $currentClip = null;

    public function copyCuePoints()
    {
        $res = true;
        foreach ($this->data->clipsDescriptionArray as $clipDescription) {
            $this->currentClip = $clipDescription;
            $res &= $this->copyCuePointsToEntry($clipDescription->sourceEntryId, $this->data->destinationEntryId);
        }
        return $res;
    }

    public function shouldCopyCuePoint($cuePoint)
    {
        $clipStartTime = $this->currentClip->startTime;
        $clipEndTime = $clipStartTime + $this->currentClip->duration;
        return is_null($cuePoint->endTime) || TimeOffsetUtils::onTimeRange($cuePoint->startTime,$cuePoint->endTime,$clipStartTime, $clipEndTime);
    }

    public function getCuePointFilter($entryId, $status = CuePointStatus::READY)
    {
        $filter = parent::getCuePointFilter($entryId, $status);
        $filter->startTimeLessThanOrEqual = $this->currentClip->startTime + $this->currentClip->duration;
        return $filter;
    }

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