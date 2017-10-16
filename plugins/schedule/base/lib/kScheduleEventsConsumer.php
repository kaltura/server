<?php
class kScheduleEventsConsumer implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectCreatedEventConsumer, kObjectErasedEventConsumer
{
    public function shouldConsumeCreatedEvent(BaseObject $object)
    {
        if ($object instanceof categoryEntry && $object->getStatus() == CategoryEntryStatus::ACTIVE)
            return true;
        if ($object instanceof ScheduleEventResource )
            return true;

        return false;
    }

    public function shouldConsumeDeletedEvent(BaseObject $object)
    {
        if ($object instanceof categoryEntry)
            return true;

        return false;
    }

    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns) && $object->getStatus() == CategoryEntryStatus::ACTIVE)
            return true;

        if ($object instanceof ScheduleEvent && in_array(ScheduleEventPeer::STATUS, $modifiedColumns) && in_array($object->getStatus(), array(ScheduleEventStatus::DELETED, ScheduleEventStatus::CANCELLED)))
            return true;

        return false;
    }

    public function shouldConsumeErasedEvent(BaseObject $object)
    {
        if ($object instanceof ScheduleEventResource)
            return true;
        return false;
    }


    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof categoryEntry)
            $this->reindexScheduleEvents($object->getEntryId());
        if ($object instanceof ScheduleEvent)
            $this->scheduleEventChanged($object, $modifiedColumns);

        return true;
    }

    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectDeleted()
     */
    public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
    {
        if ($object instanceof categoryEntry)
            $this->reindexScheduleEvents($object->getEntryId());

        return true;
    }


    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectCreated()
     */
    public function objectCreated(BaseObject $object)
    {
        if ($object instanceof categoryEntry)
            $this->reindexScheduleEvents($object->getEntryId());
        elseif ($object instanceof ScheduleEventResource)
            $this->updateScheduleEvent($object->getEventId());

        return true;
    }

    /* (non-PHPdoc)
     * @see kObjectErasedEventConsumer::objectErased()
     */
    public function objectErased(BaseObject $object)
    {
        if ($object instanceof ScheduleEventResource)
            $this->updateScheduleEvent($object->getEventId());

        return true;
    }

    public function reindexScheduleEvents($categoryEntryId)
    {
        $scheduleEvents = ScheduleEventPeer::retrieveByTemplateEntryId($categoryEntryId);
        foreach ($scheduleEvents as $scheduleEvent)
        {
            /* @var $scheduleEvent ScheduleEvent */
            $scheduleEvent->setUpdatedAt(time());
            $scheduleEvent->save();
            $scheduleEvent->indexToSearchIndex();
        }
    }

    public function updateScheduleEvent($eventId)
    {
        $scheduleEvent = ScheduleEventPeer::retrieveByPK($eventId);
        if (empty($scheduleEvent))
            return;

        $scheduleEvent->setUpdatedAt(time());
        $scheduleEvent->save();
        $scheduleEvent->indexToSearchIndex();

        if ($scheduleEvent->getRecurrenceType() == ScheduleEventRecurrenceType::RECURRING)
        {
            $scheduleEvents = ScheduleEventPeer::retrieveByParentId($scheduleEvent->getId());
            foreach ($scheduleEvents as $scheduleEvent)
            {
                /* @var $scheduleEvent ScheduleEvent */
                $scheduleEvent->indexToSearchIndex();
            }
        }
    }

    protected function scheduleEventChanged(ScheduleEvent $scheduleEvent, $modifiedColumns)
    {
        if (in_array($scheduleEvent->getStatus(), array(ScheduleEventStatus::DELETED, ScheduleEventStatus::CANCELLED)))
        {
            $scheduleEvents = ScheduleEventResourcePeer::retrieveByEventId($scheduleEvent->getId());
            foreach ($scheduleEvents as $currScheduleEvent)
            {
                /**
                 * @var ScheduleEventResource $currScheduleEvent
                 */
                $currScheduleEvent->delete();
            }
        }
    }

}