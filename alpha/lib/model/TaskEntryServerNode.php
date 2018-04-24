<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class TaskEntryServerNode extends EntryServerNode
{
    public function addTrackEntryForStatusChange($description = '')
    {
        $desc = 'TaskId='.$this->getId().' New Status='.$this->getStatus().' ServerNodeId='.$this->getServerNodeId() . $description;
        $type = TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_ENTRY_SERVER_NODE_TASK;
        $this->addTrackEntryInfo($type, $desc);
    }

    public function postInsert(PropelPDO $con = null)
    {
        $this->addTrackEntryForStatusChange("Creating");
        parent::postInsert($con);
    }
}
