<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KLiveClippingCopyCuePointEngine extends KLiveToVodCopyCuePointEngine
{
    //override set status to HANDLED as LiveToVod engine
    protected static function postProcessCuePoints($copiedCuePointIds) {}

    protected function shouldCopyCuePoint($cuePoint)
    {
        $cuePointStartTime = $this->getOffsetForTimestamp($cuePoint->createdAt * 1000, false);
        $cuePointEndTime = $this->getOffsetForTimestamp($cuePoint->calculatedEndTime * 1000, false);
        KalturaLog::debug("Checking times to know if copy is needed: start [$cuePointStartTime] end [$cuePointEndTime]");
        //TODO - need also to add check if cue point is after the clip (after adding clip duration to job data)
        return ($cuePointStartTime >= 0 || $cuePointEndTime > 0);
    }
}
