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
 * @package plugins.virtualEvent
 * @subpackage model
 */
class VirtualEventPeer extends BaseVirtualEventPeer implements IRelatedObjectPeer {
	
	/*
	 * (non-PHPdoc)
	 * @see BaseVirtualEventPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(VirtualEventPeer::STATUS, VirtualEventStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @return 	   bool|mixed|object|string
	 * @throws     PropelException Any exceptions caught during processing will be rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(VirtualEventPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
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
	 * @see BaseVirtualEventPeer::doSelect()
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
	 * Deletes entirely from the DB all occurrences of event from now on
	 * @param int $parentId
	 * @param array $exceptForIds
	 */
	public static function deleteByParentId($parentId, array $exceptForIds = null)
	{
		$criteria = new Criteria();
		$criteria->add(VirtualEventPeer::PARENT_ID, $parentId);
		$criteria->add(VirtualEventPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		
		if($exceptForIds)
			$criteria->add(VirtualEventPeer::ID, $exceptForIds, Criteria::NOT_IN);
		
		
		$virtualEvents = VirtualEventPeer::doSelect($criteria);
		VirtualEventPeer::doDelete($criteria);
		
		$now = time();
		foreach($virtualEvents as $virtualEvent)
		{
			/* @var $virtualEvent VirtualEvent */
			$virtualEvent->setStatus(VirtualEventStatus::DELETED);
			$virtualEvent->setUpdatedAt($now);
			$virtualEvent->indexToSearchIndex();
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
		$criteria->add(VirtualEventPeer::PARENT_ID, $parentId);
		$criteria->add(VirtualEventPeer::START_DATE, kApiCache::getTime(), Criteria::GREATER_THAN);
		
		if($exceptForDates)
		{
			$criteria->add(VirtualEventPeer::ORIGINAL_START_DATE, $exceptForDates, Criteria::NOT_IN);
		}
		
		$now = time();
		
		$update = new Criteria();
		$update->add(VirtualEventPeer::STATUS, VirtualEventStatus::CANCELLED);
		$update->add(VirtualEventPeer::UPDATED_AT, $now);
		
		$con = Propel::getConnection(VirtualEventPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		BasePeer::doUpdate($criteria, $update, $con);
		
		$virtualEvents = VirtualEventPeer::doSelect($criteria);
		foreach($virtualEvents as $virtualEvent)
		{
			/* @var $virtualEvent VirtualEvent */
			$virtualEvent->setStatus(VirtualEventStatus::CANCELLED);
			$virtualEvent->setUpdatedAt($now);
			$virtualEvent->indexToSearchIndex();
		}
	}
	
	/**
	 * @param int $pk
	 * @return VirtualEvent
	 */
	public static function retrieveByPKNoFilter($pk)
	{
		self::setUseCriteriaFilter(false);
		$virtualEvent = self::retrieveByPK($pk);
		self::setUseCriteriaFilter(true);
		
		return $virtualEvent;
	}
	
	/**
	 * @param int $parentId
	 * @return array<VirtualEvent>
	 */
	public static function retrieveByParentId($parentId)
	{
		$criteria = new Criteria();
		$criteria->add(VirtualEventPeer::PARENT_ID, $parentId);
		return VirtualEventPeer::doSelect($criteria);
	}
	
	
	
	/**
	 * @param int $parentId
	 * @param array $dates
	 * @return array<VirtualEvent>
	 */
	public static function retrieveByParentIdAndDates($parentId, array $dates)
	{
		$criteria = new Criteria();
		$criteria->add(VirtualEventPeer::PARENT_ID, $parentId);
		$criteria->add(VirtualEventPeer::ORIGINAL_START_DATE, $dates, Criteria::IN);
		
		return VirtualEventPeer::doSelect($criteria);
	}
	
	/**
	 * @param int $parentId
	 * @param array $startDates
	 * @param array $endDates
	 * @return array<VirtualEvent>
	 */
	public static function retrieveByParentIdAndStartAndEndDates($parentId, $startDates, $endDates)
	{
		$criteria = new Criteria();
		$criteria->add(VirtualEventPeer::PARENT_ID, $parentId);
		$criteria->add(VirtualEventPeer::START_DATE, $startDates, Criteria::IN);
		$criteria->add(VirtualEventPeer::END_DATE, $endDates, Criteria::IN);
		
		return VirtualEventPeer::doSelect($criteria);
	}
	
	/**
	 * @param string $templateEntryId
	 * @return array<VirtualEvent>
	 */
	public static function retrieveByTemplateEntryId($templateEntryId)
	{
		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
		$filter = new VirtualEventFilter();
		$filter->setTemplateEntryIdEqual($templateEntryId);
		$filter->attachToCriteria($c);
		
		return self::doSelect($c);
	}
	
	/**
	 * @param string $templateEntryId
	 * @param array $types
	 * @param int $startTime
	 * @param int $endTime
	 * @return array<VirtualEvent>
	 */
	public static function retrieveByTemplateEntryIdAndTypes($templateEntryId, $types, $startTime = null, $endTime = null)
	{
		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
		$c->add(VirtualEventPeer::TYPE, $types, Criteria::IN);
		if ($startTime) // if giving start time - ignore all the events that already finished
		{
			$c->add(VirtualEventPeer::END_DATE, $startTime - self::TIME_MARGIN, Criteria::GREATER_EQUAL);
		}
		if ($endTime) // if giving end time - ignore all future events after 6 hours margin.
		{
			$c->add(VirtualEventPeer::START_DATE, $endTime + self::TIME_MARGIN, Criteria::LESS_EQUAL);
		}
		$filter = new VirtualEventFilter();
		$filter->setTemplateEntryIdEqual($templateEntryId);
		$filter->attachToCriteria($c);
		return self::doSelect($c);
	}
	
	public static function retrieveByTemplateEntryIdAndTime($templateEntryId,
	                                                        $time = null)
	{
		$time = $time ? $time : time();
		
		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
		$c->add(VirtualEventPeer::END_DATE, $time, Criteria::GREATER_EQUAL);
		$c->add(VirtualEventPeer::START_DATE, $time, Criteria::LESS_EQUAL);
		$filter = new VirtualEventFilter();
		$filter->setTemplateEntryIdEqual($templateEntryId);
		$filter->attachToCriteria($c);
		return self::doSelect($c);
	}
	
	/**
	 * @param string $templateEntryId
	 * @param int $startTime
	 * @param int $endTime
	 * @param array $types
	 * @return array<VirtualEvent>
	 */
	public static function retrieveOtherEvents($templateEntryId, $startDate, $endDate, array $idsToIgnore)
	{
		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
		
		$criterion1 = $c->getNewCriterion(VirtualEventPeer::START_DATE, $startDate, Criteria::LESS_THAN);
		$criterion1->addAnd($c->getNewCriterion(VirtualEventPeer::END_DATE, $startDate, Criteria::GREATER_THAN));
		
		$criterion2 = $c->getNewCriterion(VirtualEventPeer::START_DATE, $endDate, Criteria::LESS_THAN);
		$criterion2->addAnd($c->getNewCriterion(VirtualEventPeer::END_DATE, $endDate, Criteria::GREATER_THAN));
		
		$criterion3 = $c->getNewCriterion(VirtualEventPeer::START_DATE, $startDate, Criteria::GREATER_EQUAL);
		$criterion3->addAnd($c->getNewCriterion(VirtualEventPeer::END_DATE, $endDate, Criteria::LESS_EQUAL));
		
		$c->addOr($criterion1);
		$c->addOr($criterion2);
		$c->addOr($criterion3);
		
		$filter = new VirtualEventFilter();
		$filter->setTemplateEntryIdEqual($templateEntryId);
		$filter->setIdsNotIn($idsToIgnore);
		$filter->attachToCriteria($c);
		
		return self::doSelect($c);
	}
	
	/**
	 * @param string $resourceIds
	 * @param date $startDate
	 * @param date $endDate
	 * @param string|null $scheduleEventIdToIgnore
	 * @return array <VirtualEvent>
	 */
	public static function retrieveEventsByResourceIdsAndDateWindow($resourceIds, $startDate, $endDate, $scheduleEventIdToIgnore = null)
	{
		$c = self::getRetrieveEventsByDateWindowCriteria($startDate, $endDate, $scheduleEventIdToIgnore);
		$filter = new VirtualEventFilter();
		$filter->setResourceIdsIn($resourceIds);
		$filter->attachToCriteria($c);
		
		return self::doSelect($c);
	}
	
	
	/**
	 * @param date $startDate
	 * @param date $endDate
	 * @param string|null $scheduleEventIdToIgnore
	 * @return KalturaCriteria
	 */
	protected static function getRetrieveEventsByDateWindowCriteria($startDate, $endDate, $scheduleEventIdToIgnore = null)
	{
		$c = KalturaCriteria::create(VirtualEventPeer::OM_CLASS);
		$c->addAnd(VirtualEventPeer::START_DATE, $endDate, Criteria::LESS_THAN);
		$c->addAnd(VirtualEventPeer::END_DATE, $startDate, Criteria::GREATER_THAN);
		$c->addAnd(VirtualEventPeer::STATUS, VirtualEventStatus::ACTIVE, Criteria::EQUAL);
		
		return $c;
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		$roots = array();
		if($object instanceof EntryVirtualEvent)
		{
			$categories =  categoryPeer::retrieveByPKs(explode(',', $object->getCategoryIds()));
			$entries =  entryPeer::retrieveByPKs(explode(',', $object->getEntryIds()));
			$roots = array_merge($categories, $entries);
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
	
	
} // VirtualEventPeer
