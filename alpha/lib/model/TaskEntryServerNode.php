<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class TaskEntryServerNode extends EntryServerNode
{
    protected function addTrackEntryInfo($trackEventType, $description, $entryId = null)
    {
        $desc = 'TaskId='.$this->getId().':Status='.$this->getStatus().':ServerNodeId='.$this->getServerNodeId().':'.$description;
        $trackEventType = $trackEventType ? $trackEventType : TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_ENTRY_SERVER_NODE_TASK;
        parent::addTrackEntryInfo($trackEventType, $desc, $entryId);
    }

}
