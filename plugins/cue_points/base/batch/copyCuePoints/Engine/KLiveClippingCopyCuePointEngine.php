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
        $cuePointStartTime = self::getOffsetForTimestamp($cuePoint->createdAt * 1000, $this->amfData, false);
        $cuePointEndTime = self::getOffsetForTimestamp($cuePoint->calculatedEndTime * 1000, $this->amfData, false);
        KalturaLog::debug("Checking times to know if copy is needed: start [$cuePointStartTime] end [$cuePointEndTime]");
        return ($cuePointStartTime >= 0 || $cuePointEndTime > 0);
    }
}
