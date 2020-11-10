<?php
/**
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */

/**
 * Validates periodically that all live entries are still broadcasting to the connected media servers
 *
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */
class KAsyncValidateLiveMediaServers extends KPeriodicWorker
{
	const ENTRY_SERVER_NODE_MIN_CREATION_TIME = 120;
	const EVENT_TIME_MARGIN_SEC = 60 * 60; // 1 hour
	const SIMULIVE_HOSTNAME = 'simulive';
	const MINIMUM_TIME_TO_PLAYABLE_SEC = 18; // 3 * default segment duration

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$this->validateLiveEntryServerNode();
		$this->validateSimuliveEntryServerNode();
	}

	protected function validateLiveEntryServerNode()
	{
		$entryServerNodeFilter = $this->getLiveEntryServerNodesFilter();

		$entryServerNodePager = self::getDefaultPager();

		$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);

		while($entryServerNodes->objects && count($entryServerNodes->objects))
		{
			foreach($entryServerNodes->objects as $entryServerNode)
			{
				/* @var $entryServerNode KalturaEntryServerNode */
				KBatchBase::tryExecuteApiCall(array('KAsyncValidateLiveMediaServers', 'validateRegisteredEntryServerNode'), array($entryServerNode->partnerId, $entryServerNode->id));
			}

			$entryServerNodePager->pageIndex++;
			$entryServerNodes = self::$kClient->entryServerNode->listAction($entryServerNodeFilter, $entryServerNodePager);
		}
	}

	protected function validateSimuliveEntryServerNode()
	{
		// getting simulive serverNode
		$simuliveServerNode = KBatchBase::tryExecuteApiCall(array('KAsyncValidateLiveMediaServers', 'getSimuliveServerNode'), array());
		if (!$simuliveServerNode)
		{
			KalturaLog::info('no simulive server node found');
			return;
		}

		//getting all esn of the simulive serverNode
		$entryServerNodes = self::getEntryServerNodes($simuliveServerNode->id);

		// getting currently live events
		$currentlyLiveEvents = self::getSimulivePlayableEvents();

		// map between templateEntryId to eventId
		$eventsMap = self::arrayColumn($currentlyLiveEvents, 'templateEntryId', 'id');
		// map between entryId to entryServerNode object
		$entryServerNodesMap = self::arrayColumn($entryServerNodes, 'entryId');

		foreach ($eventsMap as $templateEntryId => $eventId)
		{
			// event exist but there's no entry serverNode - create the entryServerNode (only for simulve - has sourceEntryId)
			if ($templateEntryId && !array_key_exists($templateEntryId, $entryServerNodesMap))
			{
				KBatchBase::tryExecuteApiCall(array('KAsyncValidateLiveMediaServers', 'registerSimuliveMediaServer'), array($templateEntryId));
			}
		}

		foreach ($entryServerNodesMap as $liveEntryId => $entryServerNode)
		{
			// entryServerNode exist but there's no event - delete the entryServerNode
			if (!array_key_exists($liveEntryId, $eventsMap))
			{
				/* @var $entryServerNode KalturaEntryServerNode */
				KBatchBase::tryExecuteApiCall(array('KAsyncValidateLiveMediaServers', 'validateRegisteredEntryServerNode'), array($entryServerNode->partnerId, $entryServerNode->id));
			}
		}
	}

	/**
	 * Return filter for live entry server nodes
	 * @return KalturaEntryServerNodeFilter
	 */
	protected function getLiveEntryServerNodesFilter()
	{
		$entryServerNodeMinCreationTime = $this->getAdditionalParams("minCreationTime");
		if(!$entryServerNodeMinCreationTime)
		{
			$entryServerNodeMinCreationTime = self::ENTRY_SERVER_NODE_MIN_CREATION_TIME;
		}

		$entryServerNodeFilter = new KalturaEntryServerNodeFilter();
		$entryServerNodeFilter->orderBy = KalturaEntryServerNodeOrderBy::CREATED_AT_ASC;
		$entryServerNodeFilter->createdAtLessThanOrEqual = time() - $entryServerNodeMinCreationTime;

		$excludeServerIds = $this->getExcludeServerIds();
		if ($excludeServerIds)
		{
			$entryServerNodeFilter->serverNodeIdNotIn = implode(',', $excludeServerIds);
		}
		return $entryServerNodeFilter;
	}

	/**
	 * Return LiveStreamScheduleEvent filter
	 * @return KalturaLiveStreamScheduleEventFilter
	 */
	protected static function getSimuliveEventsFilter()
	{
		$now = time();
		$scheduleEventFilter = new KalturaLiveStreamScheduleEventFilter();
		$scheduleEventFilter->orderBy = KalturaLiveStreamScheduleEventOrderBy::START_DATE_ASC;
		$scheduleEventFilter->startDateLessThanOrEqual = $now + self::EVENT_TIME_MARGIN_SEC;
		$scheduleEventFilter->endDateGreaterThanOrEqual = $now - self::EVENT_TIME_MARGIN_SEC;
		return $scheduleEventFilter;
	}

	/**
	 * Return default pager (pageSize : 500, pageIndex : 1)
	 * @return KalturaFilterPager
	 */
	protected static function getDefaultPager()
	{
		$entryServerNodePager = new KalturaFilterPager();
		$entryServerNodePager->pageSize = 500;
		$entryServerNodePager->pageIndex = 1;
		return $entryServerNodePager;
	}

	public static function getExcludeServerNodesFromAPI($serverTypesNotIn)
	{
		$serverNodeFilter = new KalturaServerNodeFilter();
		$serverNodeFilter->typeIn = $serverTypesNotIn;
		return self::$kClient->serverNode->listAction($serverNodeFilter);
	}


	protected function getExcludeServerIds()
	{
		$excludeServerIds = array();
		$serverTypesNotIn = $this->getAdditionalParams('serverTypesNotIn');
		if ($serverTypesNotIn)
		{
			$serverNodes = KBatchBase::tryExecuteApiCall(array('KAsyncValidateLiveMediaServers', 'getExcludeServerNodesFromAPI'), array($serverTypesNotIn));
			if ($serverNodes && $serverNodes->objects)
			{
				foreach($serverNodes->objects as $serverNode)
				{
					$excludeServerIds[] = $serverNode->id;
				}
			}
		}
		return $excludeServerIds;
	}

	/**
	 * Filter and take only the simulive (has sourceEntryId) events that currently live and playable (considering preStart / postEnd)
	 * @param array<KalturaLiveStreamScheduleEvent> $events
	 * @return array<KalturaLiveStreamScheduleEvent>
	 */
	protected static function filterSimulivePlayableEvents($events)
	{
		return array_filter($events, function ($event) {
			$now = time();
			/* @var $event KalturaLiveStreamScheduleEvent */
			return $event->sourceEntryId
				&& ($now >= ($event->startDate - $event->preStartTime + self::MINIMUM_TIME_TO_PLAYABLE_SEC))
				&& ($now <= ($event->endDate + $event->postEndTime));
		});
	}

	/**
	 * Return the serverNode with hostName == SIMULIVE_HOSTNAME
	 * @return KalturaServerNode or null if not found
	 */
	protected static function getSimuliveServerNode()
	{
		$serverNodeFilter = new KalturaLiveClusterMediaServerNodeFilter();
		$serverNodeFilter->hostNameLike = self::SIMULIVE_HOSTNAME;
		$serverNodes = self::$kClient->serverNode->listAction($serverNodeFilter);
		if (!$serverNodes || !count($serverNodes->objects))
		{
			return null;
		}
		foreach ($serverNodes->objects as $serverNode)
		{
			/* @var $serverNode KalturaServerNode */
			if ($serverNode->hostName == self::SIMULIVE_HOSTNAME)
			{
				return $serverNode;
			}
		}
		return null;
	}

	/**
	 * Return events that currently live (considering preStart / postEnd and minTimeToPlayable)
	 * @return array<KalturaLiveStreamScheduleEvent> or null
	 */
	protected static function getSimulivePlayableEvents()
	{
		$simuliveEventsMerged = array();
		$simuliveEventsFilter = self::getSimuliveEventsFilter();
		$simuliveEventsPager = self::getDefaultPager();
		$simuliveEvents = KBatchBase::tryExecuteApiCall(array('KBatchBase', 'apiListCall'), array("scheduleEvent", $simuliveEventsFilter, $simuliveEventsPager));
		while ($simuliveEvents && $simuliveEvents->objects && count($simuliveEvents->objects))
		{
			$simuliveEventsMerged = array_merge($simuliveEventsMerged, self::filterSimulivePlayableEvents($simuliveEvents->objects));
			$simuliveEventsPager->pageIndex++;
			$simuliveEvents = KBatchBase::tryExecuteApiCall(array('KBatchBase', 'apiListCall'), array("scheduleEvent", $simuliveEventsFilter, $simuliveEventsPager));
		}
		return $simuliveEventsMerged;
	}

	/**
	 * Return the entryServerNodes with serverNodeId == $serverNodeId
	 * @param string $serverNodeId
	 * @return array<KalturaEntryServerNode>
	 */
	protected static function getEntryServerNodes($serverNodeId)
	{
		$entryServerNodesMerged = array();
		$entryServerNodeFilter = new KalturaEntryServerNodeFilter();
		$entryServerNodeFilter->serverNodeIdEqual = $serverNodeId;
		$entryServerNodePager = self::getDefaultPager();
		$entryServerNodes = KBatchBase::tryExecuteApiCall(array('KBatchBase', 'apiListCall'), array("entryServerNode", $entryServerNodeFilter, $entryServerNodePager));
		while ($entryServerNodes && $entryServerNodes->objects && count($entryServerNodes->objects))
		{
			$entryServerNodesMerged = array_merge($entryServerNodesMerged, $entryServerNodes->objects);
			$entryServerNodePager->pageIndex++;
			$entryServerNodes = KBatchBase::tryExecuteApiCall(array('KBatchBase', 'apiListCall'), array("entryServerNode", $entryServerNodeFilter, $entryServerNodePager));
		}
		return $entryServerNodesMerged;
	}

	/**
	 * Return the new array with key as $id and value as $value (or the object as value if $value not given)
	 * (this function exists in PHP versions of >=5.5.0)
	 * @param array $arr - the array to manipulate on
	 * @param $id - the new id
	 * @param $value - the new value
	 * @return array or empty array if $arr isn't array
	 */
	protected static function arrayColumn($arr, $id, $value = null)
	{
		$res = array();
		if (is_array($arr))
		{
			foreach ($arr as $e)
			{
				$res[$e->$id] = $value ? $e->$value : $e;
			}
		}
		return $res;
	}

	/**
	 * Static function to call to LiveStreamService->registerMediaServer
	 * @param string $entryId
	 */
	protected static function registerSimuliveMediaServer($entryId)
	{
		self::$kClient->liveStream->registerMediaServer($entryId, self::SIMULIVE_HOSTNAME, KalturaEntryServerNodeType::LIVE_PRIMARY, null, KalturaEntryServerNodeStatus::PLAYABLE, false);
	}

	/**
	 * Static function to call to EntryServerNodeService->validateRegisteredEntryServerNode as impersonated partner
	 * @param int $impersonatedPartnerId
	 * @param int $entryServerNodeId
	 */
	protected static function validateRegisteredEntryServerNode($impersonatedPartnerId, $entryServerNodeId)
	{
		try
		{
			self::impersonate($impersonatedPartnerId);
			self::$kClient->entryServerNode->validateRegisteredEntryServerNode($entryServerNodeId);
			self::unimpersonate();
		}
		catch (KalturaException $e)
		{
			self::unimpersonate();
			KalturaLog::err("Caught exception with message [" . $e->getMessage()."]");
		}
	}

}
