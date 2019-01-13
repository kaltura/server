<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaScheduleEventResourceFilter extends KalturaScheduleEventResourceBaseFilter
{
	/**
	 * Find event-resource objects that associated with the event, if none found, find by its parent event
	 * @var int
	 */
	public $eventIdOrItsParentIdEqual;

	static private $map_between_objects = array
	(
			"eventIdOrItsParentIdEqual" => "_eq_event_id_or_parent",
	);
	
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ScheduleEventResourceFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null,
									$filterBlackoutConflicts = true)
	{
		$c = new Criteria();
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
			
		$list = ScheduleEventResourcePeer::doSelect($c);
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = ScheduleEventResourcePeer::doCount($c);
		}

		if($filterBlackoutConflicts)
		{
			$list = array_filter($list, [$this, "checkNoBlackoutConflict"]);
			$resultCount = count($list);
			if($resultCount < $pager->pageSize)
			{
				$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
			}
		}

		$response = new KalturaScheduleEventResourceListResponse();
		$response->objects = KalturaScheduleEventResourceArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}

	/**
	 * @param baseScheduleResource $baseScheduleResource
	 * @return bool
	 */
	public function checkNoBlackoutConflict($baseScheduleResource)
	{
		$scheduleEvent = BaseScheduleEventPeer::retrieveByPK($baseScheduleResource->getEventId());
		if($scheduleEvent && is_subclass_of($scheduleEvent, 'EntryScheduleEvent'))
		{
			$blackoutEvents = $scheduleEvent->getBlackoutConflicts();
			if($blackoutEvents)
			{
				return false;
			}
		}

		return true;
	}
}
