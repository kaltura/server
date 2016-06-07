<?php
class kScheduleEventsConsumer implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectCreatedEventConsumer
{
    public function shouldConsumeCreatedEvent(BaseObject $object)
    {
        if ($object instanceof categoryEntry)
        {
            return true;
        }
        return false;
    }

    public function shouldConsumeDeletedEvent(BaseObject $object)
    {
        if ($object instanceof categoryEntry)
        {
            return true;
        }
        return false;
    }

    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        try
        {
            if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns) && $object->getStatus() == categoryEntryStatus::ACTIVE)
            {
                return true;
            }
        } catch (Exception $e)
        {
            KalturaLog::err('Failed to process shouldConsumeChangedEvent - ' . $e->getMessage());
        }

        return false;
    }

    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        try
        {
            if ($object instanceof categoryEntry)
            {
                $this->reindexScheduleEvents($object->getEntryId());
            }
        } catch (Exception $e)
        {
            KalturaLog::err('Failed to process objectChangedEvent for scheduleEvent ' . $e->getMessage());
        }

        return true;
    }

    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectDeleted()
     */
    public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
    {
        try
        {
            if ($object instanceof categoryEntry)
            {
                $this->reindexScheduleEvents($object->getEntryId());
            }
        } catch (Exception $e)
        {
            KalturaLog::err('Failed to process objectDeleted for scheduleEvent ' . $e->getMessage());
        }

        return true;
    }


    /* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectCreated()
     */
    public function objectCreated(BaseObject $object)
    {
        try
        {
            if ($object instanceof categoryEntry)
            {
                $this->reindexScheduleEvents($object->getEntryId());
            }
        } catch (Exception $e)
        {
            KalturaLog::err('Failed to process objectCreated for scheduleEvent ' . $e->getMessage());
        }

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