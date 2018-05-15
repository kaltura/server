<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
class KLiveToVodCopyCuePointEngine extends KCopyCuePointEngine
{
    const MAX_CHUNK_DURATION_IN_SEC = 12;
    
    private $currentSegmentStartTime = null;
    private $currentSegmentEndTime = null;
    private $amfData = null;
    
    public function copyCuePoints()
    {

    }

    public function initEngine($data, $partnerId)
    {
        parent::initEngine($data, $partnerId);
        $amfArray = json_decode($data->amfArray);
        $this->currentSegmentStartTime = self::getSegmentStartTime($amfArray);
        $this->currentSegmentEndTime = self::getSegmentEndTime($amfArray, $data->lastSegmentDuration + $data->lastSegmentDrift) + self::MAX_CHUNK_DURATION_IN_SEC;
        self::normalizeAMFTimes($amfArray, $data->totalVodDuration, $data->lastSegmentDuration);
        $this->amfData = $amfArray;
    }

    public function shouldCopyCuePoint($cuePoint)
    {
        return true;
    }

    public function getCuePointFilter($entryId, $status = CuePointStatus::READY)
    {
        $filter = parent::getCuePointFilter($entryId, $status);
        $filter->cuePointTypeIn = 'codeCuePoint.Code,thumbCuePoint.Thumb,annotation.Annotation';
        $filter->createdAtLessThanOrEqual = $this->currentSegmentEndTime;
        if($this->data->lastCuePointSyncTime)
            $filter->createdAtGreaterThanOrEqual = $this->data->lastCuePointSyncTime;
        return $filter;
    }

    public function calculateCuePointTimes($cuePoint)
    {
        // if the cp was before the segment start time - move it to the beginning of the segment.
        $cuePointCreationTime = max($cuePoint->createdAt * 1000, $this->currentSegmentStartTime * 1000);
        $cuePointDestStartTime = self::getOffsetForTimestamp($cuePointCreationTime, $this->amfData);
        return array($cuePointDestStartTime, null);
    }
    
    protected static function postProcessCuePoints($copiedCuePointIds)
    {
        KBatchBase::$kClient->startMultiRequest();
        foreach ($copiedCuePointIds as $copiedLiveCuePointId)
            parent::executeAPICall('cuePointUpdateStatus', array($copiedLiveCuePointId, KalturaCuePointStatus::HANDLED));
        KBatchBase::$kClient->doMultiRequest();
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
        return ceil(((self::getSegmentStartTime($amfArray) * 1000) + $segmentDuration) / 1000);
    }
    // change the PTS of every amf to be relative to the beginning of the recording, and not to the beginning of the segment
    private static function normalizeAMFTimes(&$amfArray, $totalVodDuration, $currentSegmentDuration)
    {
        foreach($amfArray as $key=>$amf)
            $amfArray[$key]->pts = $amfArray[$key]->pts  + $totalVodDuration - $currentSegmentDuration;
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
}