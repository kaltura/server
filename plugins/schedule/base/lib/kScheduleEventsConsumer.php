<?php
class kScheduleEventsConsumer implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectCreatedEventConsumer
{
    public function shouldConsumeCreatedEvent(BaseObject $object)
    {
//        if ($object instanceof categoryEntry && $object->getStatus() == categoryEntryStatus::ACTIVE)
//            return true;
        return false;
    }

    public function shouldConsumeDeletedEvent(BaseObject $object)
    {
//        if ($object instanceof categoryEntry)
//            return true;
        return false;
    }

    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
//        if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns) && $object->getStatus() == categoryEntryStatus::ACTIVE)
//            return true;
        return false;
    }

    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        $this->reindexScheduleEvents($object->getEntryId());
        return true;
    }

    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectDeleted()
     */
    public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
    {
        $this->reindexScheduleEvents($object->getEntryId());
        return true;
    }


    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectCreated()
     */
    public function objectCreated(BaseObject $object)
    {
        $this->reindexScheduleEvents($object->getEntryId());
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
}