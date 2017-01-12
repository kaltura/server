<?php
/**
 * @package Scheduler
 * @subpackage SimuLiveAddScheduleEvents
 */

/**
 * add Simu-Live scheduled Events
 *
 * @package Scheduler
 * @subpackage SimuLiveAddScheduleEvents
 */
class KAsyncSimuLiveAddScheduleEvents extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SIMU_LIVE;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$currTime = time();

		$filter = new KalturaLiveChannelFilter();
		$filter->startTimeLessThenEqual = $currTime;
		$filter->endTimeGreaterThenEqual = $currTime;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		$pager->pageIndex = 1;

		$liveChannels = self::$kClient->liveChannel->listAction($filter, $pager);
		while(count($liveChannels->objects))
		{
			foreach ($liveChannels->objects as $liveChannel)
			{
				self::addScheduleEventsForChannel($liveChannel, $currTime);
			}
			$pager->pageIndex++;
			$liveChannels = self::$kClient->liveChannel->listAction($filter, $pager);
		}
	}

	/**
	 * @param $liveChannel
	 * @param $currTime
	 */
	private static function addScheduleEventsForChannel($liveChannel, $currTime)
	{
		$dvrWindow = KBatchBase::$taskConfig->params->dvrWindow;
		$forwardBufferWindow = KBatchBase::$taskConfig->params->forwardBufferWindow;
		$playListId = $liveChannel->playlistId;
		$playListEntries = self::$kClient->playlist->execute($playListId);
		$totalTime = $liveChannel->startDate;

		$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		$events = array();

		foreach ($playListEntries as $entry)
		{
			$totalTime += $entry->duration;
			if($totalTime < $currTime - $dvrWindow)
				continue;
			if($currTime + $forwardBufferWindow < $totalTime)
				break;
			//else we need to add schedule event if doesnt exist
			$startTime = $totalTime - $entry->duration;
			$endTime = $totalTime;
			self::addEvent($events, $startTime, $endTime, $playListId, $entry, $schedulePlugin);
		}

		self::impersonate($liveChannel->partnerId);
		self::$kClient->startMultiRequest();
		foreach ($events as $event)
		{
			$schedulePlugin->scheduleEvent->add($event);
		}
		self::$kClient->doMultiRequest();
		self::unimpersonate();
	}

	/**
	 * @param $events
	 * @param $startDate
	 * @param $endDate
	 * @param $playListId
	 * @param $entryId
	 * @param $schedulePlugin\
	 */
	private static function addEvent(&$events, $startDate, $endDate, $playListId, $entryId, $schedulePlugin)
	{
		$filter = new KalturaSimulatedLiveEntryScheduleEventFilter();
		$filter->entryIdsLike = $playListId;
		$filter->referenceIdEqual = $entryId;
		$filter->startDateGreaterThanOrEqual = $startDate;
		$filter->endDateLessThanOrEqual = $endDate;
		$filter->statusIn = KalturaScheduleEventStatus::ACTIVE;

		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;

		$response = $schedulePlugin->scheduleEvent->listAction($filter, $pager);
		//check if the event doesn't exist
		if($response->totalCount == 0)
		{
			$event = new KalturaSimulatedLiveEntryScheduleEvent();
			$event->startDate = $startDate;
			$event->endDate = $endDate;
			$event->entryIds = $playListId;
			$event->referenceId = $entryId;
			$events[] = $event;
		}
	}
}
