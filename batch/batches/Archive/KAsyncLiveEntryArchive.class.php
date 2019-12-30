<?php

/**
 * @package Scheduler
 * @subpackage Copy
 */

class KAsyncLiveEntryArchive extends KJobHandlerWorker
{
    const MAX_CUE_POINTS_PER_PAGE = 100;

    /* (non-PHPdoc)
     * @see KBatchBase::getType()
     */
    public static function getType()
    {
        return KalturaBatchJobType::LIVE_ENTRY_ARCHIVE;
    }

    /**
     * (non-PHPdoc)
     * @see KBatchBase::getJobType()
     */
    protected function getJobType()
    {
        return KalturaBatchJobType::LIVE_ENTRY_ARCHIVE;
    }

    /**
     * @param KalturaBatchJob $job
     * @return KalturaBatchJob
     */
    protected function exec(KalturaBatchJob $job)
    {
        KalturaLog::info("Starting live entry archive! big news!");
        $jobData = $job->data;
        /** @var KalturaLiveEntryArchiveJobData $jobData*/
        $liveEntryId = $jobData->liveEntryId;
        $notDeletedCuePointTags = $jobData->nonDeletedCuePointsTags;
        self::deleteCuePoints($liveEntryId, $notDeletedCuePointTags);

        $liveEntry = KBatchBase::$kClient->baseEntry->get($liveEntryId);
        /** @var KalturaLiveStreamEntry $liveEntry */
        $vodEntry = KBatchBase::$kClient->baseEntry->get($liveEntry->recordedEntryId);
        self::updateEntriesData($liveEntry, $vodEntry);

        self::clearPushNotificationQueue($liveEntryId, $liveEntry->partnerId);

        return $this->closeJob($job, null, null, "Copy all cue points finished", KalturaBatchJobStatus::FINISHED);
    }

    private static function clearPushNotificationQueue($liveEntryId, $partnerId)
    {
        $liveEntryString = new KalturaStringValue();
        $liveEntryString->value = $liveEntryId;
        $pushNotificationParameter = new KalturaPushEventNotificationParameter();
        $pushNotificationParameter->key = "entryId";
        $pushNotificationParameter->value = $liveEntryString;
        $pushEventNotificationParameterArray = array();
        $pushEventNotificationParameterArray[] = $pushNotificationParameter;
        $pushNotificationParams = new KalturaPushNotificationParams();
        $pushNotificationParams->userParams = $pushEventNotificationParameterArray;

        KBatchBase::impersonate($partnerId);
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand('POLLS_PUSH_NOTIFICATIONS',
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand('PUBLIC_QNA_NOTIFICATIONS',
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand('USER_QNA_NOTIFICATIONS',
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::unimpersonate();
    }

    private static function updateEntriesData(KalturaLiveStreamEntry $liveEntry, KalturaBaseEntry $vodEntry)
    {
        $currentDate = date("M-d-Y H:i");
        $updatedVodEntry = new KalturaMediaEntry();
        $updatedVodEntry->name = $liveEntry->name. " " . $currentDate;
        $updatedVodEntry->description = $liveEntry->description;
        $updatedVodEntry->tags = $liveEntry->tags;
        $updatedVodEntry->displayInSearch = 1;
        KBatchBase::$kClient->baseEntry->update($vodEntry->id, $updatedVodEntry);

        $updatedLiveEntry = new KalturaLiveStreamEntry();
        $updatedLiveEntry->redirectEntryId = "";
        $updatedLiveEntry->recordedEntryId = "";
        KBatchBase::$kClient->baseEntry->update($liveEntry->id, $updatedLiveEntry);
    }

    private static function deleteCuePoints($entryId, $notDeletedCuePointTags)
    {
        $cuePointsCount = self::getCuePointCount($entryId);
        $loopIterations = ceil($cuePointsCount / self::MAX_CUE_POINTS_PER_PAGE);
        $filter = self::getCuePointFilter($entryId);
        $pager = new KalturaFilterPager();
        $pager->pageSize = self::MAX_CUE_POINTS_PER_PAGE;

        $notDeletedCuePointTagsArray = explode(",", $notDeletedCuePointTags);

        for ($i = 1; $i <= $loopIterations; $i++)
        {
            $pager->pageIndex = $i;
            try
            {
                $cuePointsList = KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
                foreach ($cuePointsList->objects as $cuePoint)
                {
                    $cuePointTags = explode(',', $cuePoint->tags);
                    $relevantTags = array_intersect($notDeletedCuePointTagsArray, $cuePointTags);
                    if (count($relevantTags) == 0)
                    {
                        KBatchBase::$kClient->cuePoint->updateStatus($cuePoint->id, CuePointStatus::DELETED);
                    }
                }
            }
            catch (Exception $ex)
            {
                KalturaLog::err('Failed to delete cue points for live entry ' . $entryId);
            }
        }
    }

    private static function getCuePointFilter($entryId)
    {
        $filter = new KalturaCuePointFilter();
        $filter->entryIdEqual = $entryId;
        $filter->statusIn = CuePointStatus::READY;
        return $filter;
    }

    private static function getCuePointCount($entryId)
    {
        $filter = self::getCuePointFilter($entryId);
        return KBatchBase::$kClient->cuePoint->count($filter);
    }
}