<?php


/**
 * Skeleton subclass for performing query and update operations on the 'schedule_event' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.schedule
 * @subpackage model
 */
class ScheduleEventPeer extends BaseScheduleEventPeer implements IRelatedObjectPeer {

	const LIVE_STREAM_OM_CLASS = 'LiveStreamScheduleEvent';
	const RECORD_OM_CLASS = 'RecordScheduleEvent';
	
	
	// cache classes by their type
	protected static $class_types_cache = array(
		ScheduleEventType::LIVE_STREAM => self::LIVE_STREAM_OM_CLASS,
		ScheduleEventType::RECORD => self::RECORD_OM_CLASS,
	);
	
	/*
	 * (non-PHPdoc)
	 * @see BaseScheduleEventPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(ScheduleEventPeer::STATUS, ScheduleEventStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(ScheduleEventPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$typeField];
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
	
	/* (non-PHPdoc)
	 * @see BaseScheduleEventPeer::doSelect()
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}
			
		return parent::doSelect($c, $con);
	}
	
	/**
	 * Deletes entirely from the DB all occurences of event from now on
	 * @param int $parentId
	 * @param array $exceptForIds
	 */
	public static function deleteByParentId($parentId, array $exceptForIds = null)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventPeer::PARENT_ID, $parentId);
		$criteria->add(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRENCE);

		if($exceptForIds)
			$criteria->add(ScheduleEventPeer::ID, $exceptForIds, Criteria::NOT_IN);


		$scheduleEvents = ScheduleEventPeer::doSelect($criteria);
		ScheduleEventPeer::doDelete($criteria);

		$now = time();
		foreach($scheduleEvents as $scheduleEvent)
		{
			/* @var $scheduleEvent ScheduleEvent */
			$scheduleEvent->setStatus(ScheduleEventStatus::DELETED);
			$scheduleEvent->setUpdatedAt($now);
			$scheduleEvent->indexToSearchIndex();
		}
	}
	
	/**
	 * Updates the status of all occurrences to cancelled
	 * @param int $parentId
	 * @param array $exceptForDates
	 */
	public static function cancelByParentId($parentId, array $exceptForDates = null)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventPeer::PARENT_ID, $parentId);
		$criteria->add(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRENCE);
		$criteria->add(ScheduleEventPeer::START_DATE, kApiCache::getTime(), Criteria::GREATER_THAN);

		if($exceptForDates)
		{
			$criteria->add(ScheduleEventPeer::ORIGINAL_START_DATE, $exceptForDates, Criteria::NOT_IN);
		}

		$now = time();
		
		$update = new Criteria();
		$update->add(ScheduleEventPeer::STATUS, ScheduleEventStatus::CANCELLED);
		$update->add(ScheduleEventPeer::UPDATED_AT, $now);
		
		$con = Propel::getConnection(ScheduleEventPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		BasePeer::doUpdate($criteria, $update, $con);

		$scheduleEvents = ScheduleEventPeer::doSelect($criteria);
		foreach($scheduleEvents as $scheduleEvent)
		{
			/* @var $scheduleEvent ScheduleEvent */
			$scheduleEvent->setStatus(ScheduleEventStatus::CANCELLED);
			$scheduleEvent->setUpdatedAt($now);
			$scheduleEvent->indexToSearchIndex();
		}
	}
	
	/**
	 * @param int $pk
	 * @return ScheduleEvent
	 */
	public static function retrieveByPKNoFilter($pk)
	{
		self::setUseCriteriaFilter(false);
		$scheduleEvent = self::retrieveByPK($pk);
		self::setUseCriteriaFilter(true);
		
		return $scheduleEvent;
	}

	/**
	 * @param int $parentId
	 * @return array<ScheduleEvent>
	 */
	public static function retrieveByParentId($parentId)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventPeer::PARENT_ID, $parentId);
		$criteria->add(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRENCE);
		return ScheduleEventPeer::doSelect($criteria);
	}



	/**
	 * @param int $parentId
	 * @param array $dates
	 * @return array<ScheduleEvent>
	 */
	public static function retrieveByParentIdAndDates($parentId, array $dates)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventPeer::PARENT_ID, $parentId);
		$criteria->add(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRENCE);
		$criteria->add(ScheduleEventPeer::ORIGINAL_START_DATE, $dates, Criteria::IN);
		
		return ScheduleEventPeer::doSelect($criteria);
	}

	/**
	 * @param int $parentId
	 * @param array $startDates
	 * @param array $endDates
	 * @return array<ScheduleEvent>
	 */
	public static function retrieveByParentIdAndStartAndEndDates($parentId, $startDates, $endDates)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventPeer::PARENT_ID, $parentId);
		$criteria->add(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRENCE);
		$criteria->add(ScheduleEventPeer::START_DATE, $startDates, Criteria::IN);
		$criteria->add(ScheduleEventPeer::END_DATE, $endDates, Criteria::IN);

		return ScheduleEventPeer::doSelect($criteria);
	}

	/**
	 * @param string $templateEntryId
	 * @return array<ScheduleEvent>
	 */
	public static function retrieveByTemplateEntryId($templateEntryId)
	{
		$c = KalturaCriteria::create(ScheduleEventPeer::OM_CLASS);
		$filter = new ScheduleEventFilter();
		$filter->setTemplateEntryIdEqual($templateEntryId);
		$filter->attachToCriteria($c);

		return self::doSelect($c);
	}

	/**
	 * @param string $resourceIds
	 * @param date $startDate
	 * @param date $endDate
	 * @return array<ScheduleEvent>
	 */
	public static function retrieveEventsByResourceIdsAndDateWindow($resourceIds, $startDate, $endDate,$scheduleEventIdToIgnore=null)
	{
		$c = KalturaCriteria::create(ScheduleEventPeer::OM_CLASS);
		$c->addAnd(ScheduleEventPeer::START_DATE, $endDate, Criteria::LESS_THAN);
		$c->addAnd(ScheduleEventPeer::END_DATE, $startDate, Criteria::GREATER_THAN);
		$c->addAnd(ScheduleEventPeer::STATUS, ScheduleEventStatus::ACTIVE, Criteria::EQUAL);
		$c->addAnd(ScheduleEventPeer::RECURRENCE_TYPE, ScheduleEventRecurrenceType::RECURRING, Criteria::NOT_EQUAL);
		if($scheduleEventIdToIgnore)
		{
			KalturaLog::info("Ignoring  scheduleEventId {$scheduleEventIdToIgnore}");
			$c->addAnd(ScheduleEventPeer::ID, $scheduleEventIdToIgnore, Criteria::NOT_EQUAL);
			$c->addAnd(ScheduleEventPeer::PARENT_ID, $scheduleEventIdToIgnore, Criteria::NOT_IN);
		}

		$filter = new ScheduleEventFilter();
		$filter->setResourceIdsIn($resourceIds);

		$filter->attachToCriteria($c);

		return self::doSelect($c);
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		$roots = array();
		if($object instanceof EntryScheduleEvent)
		{
			$categories =  categoryPeer::retrieveByPKs(explode(',', $object->getCategoryIds()));
			$entries =  entryPeer::retrieveByPKs(explode(',', $object->getEntryIds()));
			$recurrenceObjects = array();
			if($object->getRecurrenceType()==ScheduleEventRecurrenceType::RECURRING) {
				$recurrenceObjects = self::retrieveByParentId($object->getId(), array());
			}
			$roots = array_merge($categories, $entries, $recurrenceObjects);
		}

		return $roots;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("scheduleEvent:id%s", self::ID));
	}
	
} // ScheduleEventPeer
