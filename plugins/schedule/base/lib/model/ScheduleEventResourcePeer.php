<?php


/**
 * Skeleton subclass for performing query and update operations on the 'schedule_event_resource' table.
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
class ScheduleEventResourcePeer extends BaseScheduleEventResourcePeer implements IRelatedObjectPeer {

	public static function retrieveByEventAndResource($eventId, $resourceId)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $eventId);
		$criteria->add(ScheduleEventResourcePeer::RESOURCE_ID, $resourceId);

		return ScheduleEventResourcePeer::doSelectOne($criteria);
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object ScheduleEventResource */
		
		$roots = array(
			ScheduleEventPeer::retrieveByPK($object->getEventId()),
			ScheduleResourcePeer::retrieveByPK($object->getResourceId())
		);
		
		if($object->getEntryId())
			$roots[] = entryPeer::retrieveByPK($object->getEntryId());
			
		return $roots;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
	
	public static function retrieveByEventId($eventId)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $eventId);

		return ScheduleEventResourcePeer::doSelect($criteria);
	}

	public static function retrieveByEventIdOrItsParentId($eventId)
	{
		$criteria = new Criteria();

		$scheduleEventResources = ScheduleEventResourcePeer::retrieveByEventId($eventId);
		if(!is_null($scheduleEventResources) && count($scheduleEventResources))
		{
			$scheduleEventResourceIds = array();
			foreach($scheduleEventResources as $scheduleEventResource)
			{
				/* @var $scheduleEventResource ScheduleEventResource */
				$scheduleEventResourceIds[] = $scheduleEventResource->getId();
			}
			$criteria->add(ScheduleEventResourcePeer::ID, $scheduleEventResourceIds, Criteria::IN);
		}
		else
		{
			$scheduleEvent = ScheduleEventPeer::retrieveByPK($eventId);
			if(!is_null($scheduleEvent))
			{
				if($scheduleEvent->getParentId())
					$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $scheduleEvent->getParentId(), Criteria::EQUAL);
				else
					$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $scheduleEvent->getId(), Criteria::EQUAL);
			}
		}

		return ScheduleEventResourcePeer::doSelect($criteria);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("scheduleEventResource:eventId=%s", self::EVENT_ID));		
	}
} // ScheduleEventResourcePeer
