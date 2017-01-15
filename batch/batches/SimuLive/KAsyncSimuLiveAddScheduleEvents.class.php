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
		$filter->startDateLessThanOrEqual = $currTime;
		$filter->endDateGreaterThanOrEqual = $currTime;
		
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
		self::impersonate($liveChannel->partnerId);
		$playListEntries = self::$kClient->playlist->execute($playListId);
		self::unimpersonate();

		$totalTime = $liveChannel->startDate;
		if($liveChannel->loop)
		{
			self::impersonate($liveChannel->partnerId);
			$playList = self::$kClient->playlist->get($playListId);
			self::unimpersonate();
			$playlistDuration = $playList->duration;
			$offset = floor( floor(($currTime - $totalTime) / $playlistDuration) * $playlistDuration);
			if($offset > 0)
				$totalTime += $offset;
		}

		$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		$events = array();
		$loop = true;
		while($loop)
		{
			$loop = self::addPlaylistEvents($playListEntries, $totalTime, $currTime, $dvrWindow, $forwardBufferWindow, $liveChannel, $schedulePlugin, $events);
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
	 * loops over the playlist entries and adds events in the correct offset
	 * @param $playListEntries
	 * @param $offset
	 * @param $currTime
	 * @param $dvrWindow
	 * @param $forwardBufferWindow
	 * @param $liveChannel
	 * @param $schedulePlugin
	 * @param $events
	 * @return bool
	 */
	private static function addPlaylistEvents($playListEntries, &$offset, $currTime, $dvrWindow, $forwardBufferWindow, $liveChannel ,$schedulePlugin, &$events)
	{
		foreach ($playListEntries as $entry)
		{
			$offset += $entry->duration;
			if($offset < $currTime - $dvrWindow)
				continue;
			if($currTime + $forwardBufferWindow < $offset)
				return false;
			//else we need to add schedule event if doesnt exist
			$startTime = $offset - $entry->duration;
			$endTime = $offset;
			self::addEvent($events, $startTime, $endTime, $liveChannel->id, $entry->id, $schedulePlugin);
		}

		if($liveChannel->loop)
			return true;
		return false;
	}

	/**
	 * checks if the event already exist and if not adds it
	 * @param $events
	 * @param $startDate
	 * @param $endDate
	 * @param $ChannelId
	 * @param $entryId
	 * @param $schedulePlugin
	 */
	private static function addEvent(&$events, $startDate, $endDate, $ChannelId, $entryId, $schedulePlugin)
	{
		$filter = new KalturaSimulatedLiveEntryScheduleEventFilter();
		$filter->entryIdsLike = $entryId;
		$filter->referenceIdEqual = $ChannelId;
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
			$event->entryIds = $entryId;
			$event->referenceId = $ChannelId;
			$events[] = $event;
		}
	}
}
