<?php

/**
 * @package batch
 * @subpackage Archive
 */

class KAsyncLiveEntryArchive extends KJobHandlerWorker
{
    const MAX_CUE_POINTS_PER_PAGE = 100;

    const DISPLAY_IN_SEARCH_TRUE = 1;

    const POLLS_PUSH_NOTIFICATIONS_STRING = 'POLLS_PUSH_NOTIFICATIONS';

    const PUBLIC_QNA_NOTIFICATIONS_STRING = 'PUBLIC_QNA_NOTIFICATIONS';

    const USER_QNA_NOTIFICATIONS_STRING = 'USER_QNA_NOTIFICATIONS';

	const DATE_FORMAT = 'M-d-Y H:i';

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
        $jobData = $job->data;
        /** @var KalturaLiveEntryArchiveJobData $jobData*/
        $liveEntryId = $jobData->liveEntryId;
        $liveEntry = KBatchBase::$kClient->baseEntry->get($liveEntryId);
        /** @var KalturaLiveStreamEntry $liveEntry */
        $notDeletedCuePointTags = $liveEntry->recordingOptions->nonDeletedCuePointsTags;
        $this->deleteCuePoints($liveEntryId, $notDeletedCuePointTags);

        $vodEntry = KBatchBase::$kClient->baseEntry->get($liveEntry->recordedEntryId);
        $this->updateEntriesData($liveEntry, $vodEntry);

        $this->clearPushNotificationQueue($liveEntryId, $liveEntry->partnerId);

        return $this->closeJob($job, null, null, "Auto archive finished", KalturaBatchJobStatus::FINISHED);
    }

    protected function clearPushNotificationQueue($liveEntryId, $partnerId)
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
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand(self::POLLS_PUSH_NOTIFICATIONS_STRING,
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand(self::PUBLIC_QNA_NOTIFICATIONS_STRING,
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::$kClient->eventNotificationTemplate->sendCommand(self::USER_QNA_NOTIFICATIONS_STRING,
            $pushNotificationParams, KalturaPushNotificationCommandType::CLEAR_QUEUE);
        KBatchBase::unimpersonate();
    }

	protected function updateEntriesData(KalturaLiveStreamEntry $liveEntry, KalturaBaseEntry $vodEntry)
	{
		$updatedVodEntry = new KalturaMediaEntry();
		$broadcastStartTime = $liveEntry->lastBroadcast;
		if ($broadcastStartTime)
		{
			$broadcastStartDate = gmdate(self::DATE_FORMAT, $broadcastStartTime);
		}
		else
		{
			$broadcastStartDate = date(self::DATE_FORMAT);
		}
		$updatedVodEntry->name = $liveEntry->name. ' ' . $broadcastStartDate;
		$updatedVodEntry->description = $liveEntry->description;
		$updatedVodEntry->tags = $liveEntry->tags;
		$updatedVodEntry->displayInSearch = self::DISPLAY_IN_SEARCH_TRUE;
		KBatchBase::$kClient->baseEntry->update($vodEntry->id, $updatedVodEntry);

		$updatedLiveEntry = new KalturaLiveStreamEntry();
 		$updatedLiveEntry->redirectEntryId = '';
		$updatedLiveEntry->recordedEntryId = '';
		KBatchBase::$kClient->baseEntry->update($liveEntry->id, $updatedLiveEntry);
	}

    protected function deleteCuePoints($entryId, $notDeletedCuePointTags)
    {
        $cuePointsCount = $this->getCuePointCount($entryId);
        $loopIterations = ceil($cuePointsCount / self::MAX_CUE_POINTS_PER_PAGE);
        $filter = $this->getCuePointFilter($entryId);
        $pager = new KalturaFilterPager();
        $pager->pageSize = self::MAX_CUE_POINTS_PER_PAGE;
        $notDeletedCuePointTagsArray = self::getFixedExplodedTagsArray($notDeletedCuePointTags);
        for ($i = 1; $i <= $loopIterations; $i++)
        {
            $pager->pageIndex = $i;
            try
            {
                $cuePointsList = KBatchBase::$kClient->cuePoint->listAction($filter, $pager);
                KBatchBase::$kClient->startMultiRequest();
                foreach ($cuePointsList->objects as $cuePoint)
                {
                    $cuePointTags = self::getFixedExplodedTagsArray($cuePoint->tags);
                    $relevantTags = array_intersect($notDeletedCuePointTagsArray, $cuePointTags);
                    if (count($relevantTags) == 0)
                    {
                        KBatchBase::$kClient->cuePoint->updateStatus($cuePoint->id, CuePointStatus::DELETED);
                    }
                }
                KBatchBase::$kClient->doMultiRequest();
            }
            catch (Exception $ex)
            {
                KalturaLog::err('Failed to delete cue points for live entry ' . $entryId);
            }
        }
    }

    protected static function getFixedExplodedTagsArray($tagsAsString)
    {
        if (empty($tagsAsString))
        {
            return array();
        }
        else
        {
            return explode(",", $tagsAsString);
        }
    }

    protected function getCuePointFilter($entryId)
    {
        $filter = new KalturaCuePointFilter();
        $filter->entryIdEqual = $entryId;
        return $filter;
    }

    protected function getCuePointCount($entryId)
    {
        $filter = $this->getCuePointFilter($entryId);
        return KBatchBase::$kClient->cuePoint->count($filter);
    }
}