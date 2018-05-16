<?php
/**
 * @package plugins.cuePoints
 * @subpackage Scheduler
 */
abstract class KCopyCuePointEngine
{
    const API_CALL_ATTEMPTS = 3;
    const API_INTERVAL = 5;
    const MAX_CUE_POINT_CHUNKS = 500;
    
    protected $data = null;
    protected $partnerId = null;

    abstract public function copyCuePoints();

    abstract public function shouldCopyCuePoint($cuePoint);

    abstract public function calculateCuePointTimes($cuePoint);

    protected function validateJobData() {return true;}

    protected static function postProcessCuePoints($copiedCuePointIds) {}

    protected function copyCuePointsToEntry($srcEntryId, $destEntryId)
    {
        $filter = $this->getCuePointFilter($srcEntryId);
        $pager = $this->getCuePointPager();
        $clonedCuePointIds = array();
        do 
        {
            KalturaLog::debug("Getting list of cue point for entry [$srcEntryId] with pager index: " . $pager->pageIndex);
            $res = self::executeAPICall('cuePointList', array($filter, $pager));
            if (!$res)
                return false;
            foreach ($res->objects as $cuePoint)
            {
                if (!$this->shouldCopyCuePoint($cuePoint))
                    continue;
                $clonedCuePoint = self::executeAPICall('cuePointClone', array($cuePoint->id, $destEntryId));
                if ($clonedCuePoint)
                {
                    list($startTime, $endTime) = $this->calculateCuePointTimes($cuePoint);
                    $res = self::executeAPICall('updateCuePointTimes', array($clonedCuePoint->id, $startTime, $endTime));
                    if ($res)
                        $clonedCuePointIds[] = $cuePoint->id;
                    else
                        KalturaLog::info("Update time for [{$cuePoint->id}] of [$startTime, $endTime] - Failed");
                } else
                    KalturaLog::info("Could not copy [{$cuePoint->id}] - moving to next");
            }
            $pager->pageIndex++;
            
        } while ($res && count($res->objects));
        $this->postProcessCuePoints($clonedCuePointIds);
        return true;
    }

    public function initEngine($data, $partnerId) 
    {
        $this->data = $data;
        $this->partnerId = $partnerId;
    }

    public static function getEngine($copyCuePointJobType, $data, $partnerId) {
        $engine = null;
        switch($copyCuePointJobType)
        {
            case CopyCuePointJobType::MULTI_SOURCES:
               $engine = new KMultiSourceCopyCuePointEngine();
                break;
            case CopyCuePointJobType::LIVE:
                $engine = new KLiveToVodCopyCuePointEngine();
                break;
        }
        if (!$engine)
            return null;
        $engine->initEngine($data, $partnerId);
        return $engine;
    }

    protected function getCuePointFilter($entryId, $status = CuePointStatus::READY)
    {
        $filter = new KalturaCuePointFilter();
        $filter->entryIdEqual = $entryId;
        $filter->statusIn = $status;
        $filter->orderBy = '+createdAt';
        return $filter;
    }

    protected function getCuePointPager()
    {
        $pager = new KalturaFilterPager();
        $pager->pageIndex = 0;
        $pager->pageSize = self::MAX_CUE_POINT_CHUNKS;
        return $pager;
    }

    protected static function executeAPICall($functionName, $params)
    {
        $attempts = self::API_CALL_ATTEMPTS;
        while ($attempts-- > 0)
        {
            try {
                $res = call_user_func_array("self::$functionName", $params);
                if (KBatchBase::$kClient->isError($res))
                    throw new APIException($res);
                return $res;
            }
            catch  (Exception $ex) {
                KalturaLog::warning("API Call for [$functionName] failed number of retires " . $attempts);
                KalturaLog::err($ex->getMessage());
                sleep(self::API_INTERVAL);
            }
        }
        return false;
    }

    private static function updateCuePointTimes($cuePointId, $startTime, $endTime = null)
    {
        return KBatchBase::$kClient->cuePoint->updateCuePointsTimes($cuePointId, $startTime,$endTime);
    }
    
    private function cuePointList($filter, $pager)
    {
        return KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
    }

    private function cuePointClone($cuePointId, $destinationEntryId)
    {
        return KBatchBase::$kClient->cuePoint->cloneAction($cuePointId, $destinationEntryId);
    }

    private function cuePointUpdateStatus($cuePointId, $newStatus)
    {
        return KBatchBase::$kClient->cuePoint->updateStatus($cuePointId, $newStatus);
    }
}
